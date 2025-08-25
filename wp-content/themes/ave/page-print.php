<?php
/**
 * Template Name: Print PDF
 * Description: Page pour l’envoi d’un fichier PDF vers l’imprimante et coworkingmetz, avec zone de dépôt (drag & drop).
 */

if ( ! is_user_logged_in() ) {
    wp_redirect( site_url( '/mon-compte/?redirect_to=' ) . urlencode( $_SERVER['REQUEST_URI'] ) );
    exit;
}

get_header();

global $post;


/**
 * Compte le nombre de pages d’un PDF sans Imagick (fallback pur PHP).
 *
 * Analyse le contenu et compte les occurrences des objets /Page.
 *
 * @param string $file Chemin du fichier PDF.
 * @return int|false Nombre de pages, ou false en cas d’erreur/lecture impossible.
 */
function ppu_count_pdf_pages( $file ) {
    // Sécurité/perf : limite raisonnable (ex. 30 Mo)
    $max_bytes = 30 * 1024 * 1024;
    if ( ! is_readable( $file ) ) {
        return false;
    }
    $size = filesize( $file );
    if ( $size === false || $size > $max_bytes ) {
        // Trop gros ou taille inconnue : on abandonne proprement
        return false;
    }

    $pdf = @file_get_contents( $file );
    if ( $pdf === false || $pdf === '' ) {
        return false;
    }

    // Compte des pages via les balises /Type /Page (sans confondre /Pages)
    // \b pour éviter de matcher /Pages, utilise un lookahead négatif si besoin.
    // Pattern robuste : /\/Type\s*\/Page\b(?!s)/
    if ( preg_match_all( '/\/Type\s*\/Page\b(?!s)/', $pdf, $m ) ) {
        return count( $m[0] );
    }

    return false;
}


if (isset( $_POST['ppu_nonce'] ) && wp_verify_nonce( $_POST['ppu_nonce'], 'ppu_upload_pdf' ) ) {
    $file = $_FILES['ppu_pdf'] ?? null;

    if ( ! $file || $file['error'] !== UPLOAD_ERR_OK ) {
        wp_redirect_notification( get_permalink(), [
            'type'  => 'error',
            'titre' => 'Aucun fichier',
            'texte' => 'Veuillez sélectionner un fichier PDF.',
            'temporaire' => true,
        ] );
    }

    $check = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], [ 'pdf' => 'application/pdf' ] );
    if ( $check['ext'] !== 'pdf' ) {
        wp_redirect_notification( get_permalink(), [
            'type'  => 'error',
            'titre' => 'Type de fichier invalide',
            'texte' => 'Seuls les fichiers PDF sont acceptés.',
            'temporaire' => true,
        ] );
    }

    $pages = ppu_count_pdf_pages( $file['tmp_name'] );

    if ( $pages === false ) {
        wp_redirect_notification( get_permalink(), [
            'type'  => 'error',
            'titre' => 'Lecture impossible',
            'texte' => 'Impossible de lire le PDF. Essayez un autre fichier.',
            'temporaire' => true,
        ] );
    }
    if ( intval( $pages ) > 5 ) {
        wp_redirect_notification( get_permalink(), [
            'type'  => 'error',
            'titre' => 'PDF trop long',
            'texte' => 'Votre fichier contient ' . intval( $pages ) . ' pages. Maximum autorisé : 5.',
            'temporaire' => true,
        ] );
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    $movefile = wp_handle_upload( $file, [ 'test_form' => false, 'mimes' => [ 'pdf' => 'application/pdf' ] ] );

    if ( isset( $movefile['error'] ) ) {
        wp_redirect_notification( get_permalink(), [
            'type'  => 'error',
            'titre' => 'Échec du téléversement',
            'texte' => esc_html( $movefile['error'] ),
            'temporaire' => true,
        ] );
    }

    $user    = wp_get_current_user();
    $tos      = [ '52501151653@print.brother.com', 'coworkingmetz@gmail.com', $user->user_email ];
	foreach($tos as $to) {
		$subject = sprintf(
			'Demande d\'impression faite par %s (ID:%d, %s)',
			$user->display_name ?: $user->user_login,
			$user->ID,
			$user->user_email
		);
		$body    = "Une demande d'impression PDF a été faite par {$user->display_name} ({$user->user_email}).\nPages: {$pages}";
		$headers = [ 'Content-Type: text/plain; charset=UTF-8' ];


		$sent = wp_mail( $to, $subject, $body, $headers, [ $movefile['file'] ] );
	}
    if ( $sent ) {
        wp_redirect_notification( get_permalink(), [
            'type'  => 'success',
            'titre' => 'Impression envoyée',
            'texte' => 'Votre PDF (' . intval( $pages ) . ' pages) a été envoyé pour impression.',
            'temporaire' => true,
        ] );
    } else {
        wp_redirect_notification( get_permalink(), [
            'type'  => 'error',
            'titre' => 'Échec de l’envoi',
            'texte' => 'Échec de l’envoi de l’e-mail. Contactez l’administrateur.',
            'temporaire' => true,
        ] );
    }
	exit;
}
?>

<style>
.pu-wrap{max-width:720px;margin:2rem auto;padding:0 1rem;}
.pu-dropzone{
  border:2px dashed #7f8c8d;border-radius:12px;padding:28px;text-align:center;cursor:pointer;
  transition:border-color .2s, background-color .2s, box-shadow .2s;
}
.pu-dropzone:focus{outline:none;box-shadow:0 0 0 3px rgba(100,150,250,.4);}
.pu-dropzone.is-dragover{background:#f7faff;border-color:#2980b9;}
.pu-dropzone .pu-icon{font-size:40px;line-height:1;margin-bottom:.25rem;display:block;}
.pu-helper{color:#6c7a89;margin:.25rem 0 .5rem;}
.pu-filename{margin-top:.5rem;font-weight:600;word-break:break-all;}
.pu-actions{margin-top:1rem}
.pu-hidden{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);clip-path:inset(50%);}
</style>

<div class="pu-wrap">
    <div class="entry-content">
        <?php the_content(); ?>
    </div>

    <form id="pu-form" method="post" enctype="multipart/form-data" class="pu-form" style="margin-top:1.5rem;">
        <input class="pu-hidden" type="file" name="ppu_pdf" id="ppu_pdf" accept="application/pdf" required>

        <div id="pu-dropzone" class="pu-dropzone" tabindex="0" role="button" aria-controls="ppu_pdf" aria-label="<?php esc_attr_e('Déposer un PDF (max 5 pages) ou cliquer pour sélectionner','ppu'); ?>">
            <span class="pu-icon" aria-hidden="true">📄</span>
            <div class="pu-helper">Déposez votre PDF ici (max 5 pages)</div>
            <div class="pu-helper">ou cliquez pour choisir un fichier</div>
            <div id="pu-filename" class="pu-filename" aria-live="polite"></div>
        </div>

        <?php wp_nonce_field( 'ppu_upload_pdf', 'ppu_nonce' ); ?>

        <div class="pu-actions">
            <button type="submit" id="pu-submit" name="ppu_submit" class="btn btn-solid btn-bordered border-thin" disabled>
                <span>Envoyer à l’imprimante</span>
            </button>
            <button type="button" class="btn btn-bordered border-thin" id="pu-clear" style="margin-left:.5rem;" hidden><span>Effacer</span></button>
        </div>
    </form>
</div>

<script>
(function(){
  const dz = document.getElementById('pu-dropzone');
  const input = document.getElementById('ppu_pdf');
  const fileNameEl = document.getElementById('pu-filename');
  const submitBtn = document.getElementById('pu-submit');
  const clearBtn = document.getElementById('pu-clear');
  const form = document.getElementById('pu-form');

  function setFile(file){
    if(!file) return;
    if(file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')){
      fileNameEl.textContent = 'Seuls les fichiers PDF sont acceptés.';
      submitBtn.disabled = true;
      clearBtn.hidden = true;
      return;
    }
    const dt = new DataTransfer();
    dt.items.add(file);
    input.files = dt.files;

    fileNameEl.textContent = file.name;
    submitBtn.disabled = false;
    clearBtn.hidden = false;
  }

  dz.addEventListener('click', () => input.click());
  dz.addEventListener('keydown', (e) => {
    if(e.key === 'Enter' || e.key === ' '){
      e.preventDefault();
      input.click();
    }
  });

  input.addEventListener('change', () => {
    setFile(input.files[0]);
  });

  ['dragenter','dragover'].forEach(evt => {
    dz.addEventListener(evt, (e) => {
      e.preventDefault(); e.stopPropagation();
      dz.classList.add('is-dragover');
    });
  });
  ['dragleave','dragend','drop'].forEach(evt => {
    dz.addEventListener(evt, (e) => {
      if(evt !== 'drop'){ dz.classList.remove('is-dragover'); }
    });
  });
  dz.addEventListener('drop', (e) => {
    e.preventDefault(); e.stopPropagation();
    dz.classList.remove('is-dragover');
    const file = e.dataTransfer.files && e.dataTransfer.files[0];
    setFile(file);
  });

  clearBtn.addEventListener('click', () => {
    input.value = '';
    fileNameEl.textContent = '';
    submitBtn.disabled = true;
    clearBtn.hidden = true;
  });

  form.addEventListener('submit', () => {
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span>Envoi…</span>';
  });
})();
</script>

<?php
get_footer();
