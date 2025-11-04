<?php
if ( ! is_user_logged_in() ) {
    wp_redirect( site_url( '/mon-compte/?redirect_to=' ) . urlencode( $_SERVER['REQUEST_URI'] ) );
    exit;
}

get_header();
echo '<style>.titlebar-inner { display:none !important }</style>';
	while ( have_posts() ) : the_post();

		liquid_get_content_template();
?>
<style>
.annuaire {
	margin-bottom:2rem;
}
.annuaire-item {
	display:grid;
		grid-template-columns: 50px auto;
	@media (min-width: 1024px) {
		grid-template-columns: 150px auto;
	}
	gap:2rem;
	border-top:1px solid black;
	padding-top:2rem;
	margin-top:2rem;
	>div {
		display:flex;
		flex-direction:column;
		gap:1rem;
	}
}
</style>
<div class="annuaire">
<?php
$users = get_annuaire_users();

foreach($users as $user) {
	?>
	<div class="annuaire-item">
		<figure><img src="https://photos.coworking-metz.fr/polaroid/size/medium/<?=$user->ID;?>.jpg"></figure>
		<div>
			<div class="annuaire-nom"><strong><?=$user->display_name;?></strong> 
			<span class="annuaire-competences"><em><?=$user->annuaire_competencess;?></em></span></div>
			<div class="annuaire-description"><?=nl2br(htmlspecialchars($user->annuaire_description));?></div>
			<div class="annuaire-contact">Contact: <a href="mailto:<?=$user->annuaire_contact;?>"><?=$user->annuaire_contact;?></a></div>
		</div>
	</div>
	<?php 
}
?>

</div>
<?php

	endwhile;

get_footer();
