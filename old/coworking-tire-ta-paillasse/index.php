<?php
/**
 * For Coworking Metz
 */


 /**
  * FIX rotation EXIF mobile upload
  */

//   function correctImageOrientation($filename) {
//     if (function_exists('exif_read_data')) {
//       $exif = exif_read_data($filename);
//       if($exif && isset($exif['Orientation'])) {
//         $orientation = $exif['Orientation'];
//         if($orientation != 1){
//           $img = imagecreatefromjpeg($filename);
//           $deg = 0;
//           switch ($orientation) {
//             case 3:
//               $deg = 180;
//               break;
//             case 6:
//               $deg = 270;
//               break;
//             case 8:
//               $deg = 90;
//               break;
//           }
//           if ($deg) {
//             $img = imagerotate($img, $deg, 0);        
//           }
//           // then rewrite the rotated image back to the disk as $filename 
//           imagejpeg($img, $filename, 95);
//         } // if there is some rotation necessary
//       } // if have the exif orientation info
//     } // if function exists      
//   }



class Coworking
{
    const IMAGE_MASK = 'assets/polaroid.png';
    const IMAGE_CONTENT_WIDTH = 465;
    const IMAGE_CONTENT_HEIGHT = 465;
    const IMAGE_FINALE_WIDTH = 533;
    const IMAGE_FINALE_HEIGHT = 633;

    /**
	 * Create the empty buffer image
	 *
     * @param number $width
     * @param number $height
     * @return GdImage
     */
    private function createEmptyImage($width, $height)
    {
        $img = imagecreatetruecolor($width, $height);
        $color = imagecolorallocate($img, 255, 255, 255);
        imageFill($img, 0, 0, $color);

        return $img;
    }

    /**
     * Resize and crop the upload image file
     *
     * @param string $filename
     * @param number $width
     * @param number $height
     * @return Resource
     */
    private static function resizeImageResource($filename, $width, $height)
    {
        list($wOrig, $hOrig) = getimagesize($filename);

        // Picture is too small!
        if ($wOrig < $width || $hOrig < $height) {
            throw new \Exception('Image trop petite');
        }

		$ratio = max($width / $wOrig, $height / $hOrig);
        $w = $width / $ratio;
		$h = $height / $ratio;
		if ($wOrig > $hOrig) {
            $x = ($wOrig - $width / $ratio) / 2;
            $y = 0;
        }
		else {
            $x = 0;
            $y = ($hOrig - $height / $ratio) / 2;
		}

        $mimetype = mime_content_type($filename);
        switch ($mimetype) {
            case 'image/bmp':
                $src = imagecreatefromwbmp($filename);
                break;
            case 'image/gif':
                $src = imagecreatefromgif($filename);
                break;
            case 'image/jpeg':
                $src = imagecreatefromjpeg($filename);
                break;
            case 'image/png':
                $src = imagecreatefrompng($filename);
                break;
            default :
                throw new \Exception('Format d\'image non supporté');
                // Unsupported picture type!
                return;
        }
        $dst = imagecreatetruecolor($width, $height);

        // Force background color to white color (replace transparency)
        $kek = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $kek);

        imagecopyresampled($dst, $src, 0, 0, $x, $y, $width, $height, $w, $h);

        imagedestroy($src);

        return $dst;
    }

    /**
	 * Calculate de bounding box
	 *
     * @param number $fontSize
     * @param number $fontAngle
     * @param string $fontFile
     * @param string $text
     * @return array
     */
    private function calculateTextBox($fontSize, $fontAngle, $fontFile, $text)
    {
        $rect = imageftbbox($fontSize, $fontAngle, $fontFile, $text);

        $minX = min([$rect[0], $rect[2], $rect[4], $rect[6]]);
        $maxX = max([$rect[0], $rect[2], $rect[4], $rect[6]]);
        $minY = min([$rect[1], $rect[3], $rect[5], $rect[7]]);
        $maxY = max([$rect[1], $rect[3], $rect[5], $rect[7]]);

        return [
            'left' => abs($minX) - 1,
            'top' => abs($minY) - 1,
            'width' => $maxX - $minX,
            'height' => $maxY - $minY,
            'box' => $rect
        ];
    }

    /**
	 * Uppercase the username
     */
    private function ucname($string) {
        $string =ucwords(strtolower($string));
    
        foreach (array('-', '\'') as $delimiter) {
          if (strpos($string, $delimiter)!==false) {
            $string =implode($delimiter, array_map('ucfirst', explode($delimiter, $string)));
          }
        }
        return $string;
    }
    
    /**
	 * Write the username on picture
	 *
     * @param number $width
     * @param number $height
     * @param string $text
     * @return GdImage
     */
    private function createUsernameBox($width, $height, $text)
    {
        $font = './assets/Andalusia.otf';
        $fontSize = 29;

        //$string =ucwords(strtolower($text));
         
        $text = $this->ucname($text);

        //$text = $string;

        $im = imagecreatetruecolor($width, $height);
        imagealphablending($im, true);
        imagesavealpha($im, true);
        imagefill($im, 0, 0, 0x7fff0000);

//        $kek = imagecolorallocate($im, 255, 0, 0);
//        imagefill($im, 0, 0, $kek);

        $bbox = $this->calculateTextBox($fontSize, 0, $font, $text);
		$x = ($width - $bbox['width']) / 2;
        $y = imagesy($im) / 2;

        $black = imagecolorallocate($im, 0, 0, 0);
		imagettftext($im, $fontSize, 0, $x, $y, $black, $font, $text);

        return $im;
    }

    /**
	 * Write the job on picture
	 *
     * @param number $width
     * @param number $height
     * @param string $text
	 *
     * @return GdImage
     */
    private function createJobBox($width, $height, $text)
    {
        $font = './assets/EvelethCleanThin.otf';
        $fontSize = 19;

        $text = strtoupper($text);

        $im = imagecreatetruecolor($width, $height);
        imagealphablending($im, true);
        imagesavealpha($im, true);
        imagefill($im, 0, 0, 0x7fff0000);

//        $kek = imagecolorallocate($im, 255, 0, 0);
//        imagefill($im, 0, 0, $kek);

		$bbox = $this->calculateTextBox($fontSize, 0, $font, $text);
        $x = ($width - $bbox['width']) / 2;
        $y = imagesy($im) / 2;

        $black = imagecolorallocate($im, 0, 0, 0);
		imagettftext($im, $fontSize, 0, $x, $y, $black, $font, $text);

        return $im;
    }

    /**
	 * Write the job2 on picture
	 *
     * @param number $width
     * @param number $height
     * @param string $text
	 *
     * @return GdImage
     */
    private function createJobBox2($width, $height, $text)
    {
        $font = './assets/EvelethCleanThin.otf';
        $fontSize = 15;

        $text = strtoupper($text);

        $im = imagecreatetruecolor($width, $height);
        imagealphablending($im, true);
        imagesavealpha($im, true);
        imagefill($im, 0, 0, 0x7fff0000);

//        $kek = imagecolorallocate($im, 255, 0, 0);
//        imagefill($im, 0, 0, $kek);

		$bbox = $this->calculateTextBox($fontSize, 0, $font, $text);
        $x = ($width - $bbox['width']) / 2;
        $y = imagesy($im) / 2;

        $black = imagecolorallocate($im, 0, 0, 0);
		imagettftext($im, $fontSize, 0, $x, $y, $black, $font, $text);

        return $im;
    }

    /**
	 * Get the filename from username
	 *
     * @param string $username
     * @return string
     */
    private function generateFilename($username)
    {
        return preg_replace("/[^a-zA-Z0-9]+/", "", strtolower($username));
    }

    /**
	 * Set the response header
	 *
     * @param string $username
     * @param boolean $display
     * @return void
     */
    private function addResponseHeaders($username, $display = false)
    {
		if ($display) {
            header('Content-Type: image/jpeg');
        }
		else {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $this->generateFilename($username) . '-polaroid.jpg');
            header('Content-Transfer-Encoding: binary');
            header('Connection: Keep-Alive');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
        }
    }

    /**
	 * Generate the picture
	 *
     * @param string $username
     * @param string $job
     * @param string $job2
     * @param string $file
     * @param number $quality
     */
    private function manageImage($username, $job, $job2, $file, $quality = 90)
    {
        $finaleImg = $this->createEmptyImage(self::IMAGE_FINALE_WIDTH, self::IMAGE_FINALE_HEIGHT);
		if ($file) {
            $src = $this->resizeImageResource($file, self::IMAGE_CONTENT_WIDTH, self::IMAGE_CONTENT_HEIGHT, true);
        }
		else {
            $src = imagecreatefromjpeg('assets/default.jpg');
		}

        imagecopy($finaleImg, $src, 34, 34, 0, 0, self::IMAGE_CONTENT_WIDTH, self::IMAGE_CONTENT_HEIGHT);

        $mask = imagecreatefrompng(self::IMAGE_MASK);
        imagecopy($finaleImg, $mask, 0, 0, 0, 0, self::IMAGE_FINALE_WIDTH, self::IMAGE_FINALE_HEIGHT);

        $text1 = $this->createUsernameBox(self::IMAGE_FINALE_WIDTH - 68, 60, $username);
        imagecopy($finaleImg, $text1, 34, 534, 0, 0, self::IMAGE_FINALE_WIDTH - 68, 60);

        $text2 = $this->createJobBox(self::IMAGE_FINALE_WIDTH - 68, 50, $job);
        imagecopy($finaleImg, $text2, 34, 574, 0, 0, self::IMAGE_FINALE_WIDTH - 68, 50);

        $text3 = $this->createJobBox2(self::IMAGE_FINALE_WIDTH - 68, 50, $job2);
        imagecopy($finaleImg, $text3, 34, 598, 0, 0, self::IMAGE_FINALE_WIDTH - 68, 50);

        $this->addResponseHeaders($username);
        imagejpeg($finaleImg, null, $quality);

        imagedestroy($mask);
        imagedestroy($src);
        imagedestroy($finaleImg);
        exit;
    }

    /**
	 * Display the form
	 *
     * @param array $data
     * @param string $error
     */
    private function displayForm($data, $error)
    {
        ?>
		<html lang="fr">
		<head>
			<title>Tire ton portrait - Le Poulailler</title>
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
				  integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3"
				  crossorigin="anonymous">
			<style>
                .form {
                    border: 1px solid #dee2e6;
                    border-radius: 5px;
                }

                .img-thumbnail {
                    width: <?php echo self::IMAGE_CONTENT_WIDTH; ?>px;
                    background-size: cover;
                    background-position: center;
                }
                img#picture {
                    width: 50%;
                }
                .text-center{
                    text-align: center;
                    font-weight: bold;
                    font-size: 15px;
                }
                .website {
                background-color: #eab234;
                border: none;
                }
                .website:hover {
                background-color: #D2A02E;
                border: none;
                }
                .website a {
                    color: #ffffff;
                    text-decoration: none;
                }
			</style>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.css" rel="stylesheet">
            <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.js" ></script>
		</head>
		<body>
            
		<div class="container">
			<div class="row">
				<div class="col-lg-6 offset-lg-3 col-12">
					<form method="post" class="form shadow mt-3 p-3" enctype="multipart/form-data">
						<div class="text-center mb-3 mt-3">
							<img src="assets/default.jpg" class="img-thumbnail" alt="Image" id="picture">
						</div>
                        <?php if ($error): ?>
							<div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <p class="text-center">Choisissez <strong>une photo de vous</strong>, renseignez les informations et 'Générez votre Polaroïd'.
                            <br/>Ajoutez ensuite le polaroïd généré ci-dessus.
                        </p>
						<div class="mb-3">
							<label for="picture_file" class="form-label">Choisissez votre photo</label>
							<input class="form-control" type="file" id="picture_file" name="picture" accept="image/*" />
						</div>
						<div class="mb-3">
							<input type="text" class="form-control" id="username" name="username" placeholder="Prénom & Nom * (dans cet ordre)"
								   value="<?php echo strip_tags($data['username']); ?>" maxlength="26" required />
						</div>
						<div class="mb-3">
							<input type="text" class="form-control" id="job" name="job" maxlength="24" placeholder="Profession *"
								   value="<?php echo htmlspecialchars(strip_tags($data['job'])); ?>"  />
						</div>
                        <div class="mb-5">
							<input type="text" class="form-control" id="job2" name="job2" maxlength="30" placeholder="Profession 2 (si besoin)"
								   value="<?php echo htmlspecialchars(strip_tags($data['job2'])); ?>" />
						</div>
						<div class="row">
							<div class="col-lg-6 col-12 mb-5">
								<button type="submit" class="btn btn-primary w-100 website" name="submit" value="save">
									1. Générer mon polaroïd
								</button>
							</div>
							<!-- <div class="col-lg-6 col-12 mb-5">
                            <button class="btn btn-secondary w-100 website"><a href="https://www.coworking-metz.fr/mon-compte/ma-trombine/">2. Retourner sur mon compte</a></button>
							</div> -->
                            <!-- <button type="reset" class="btn btn-secondary w-100" id="reset-form">
								Ré-initialiser
							</button> -->
						</div>
					</form>
				</div>
			</div>
		</div>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<script>
			$('#reset-form').click(function() {
                var $picture = $('#picture');

                $picture.attr('src', 'assets/default.jpg');
                $picture.css('background-image', 'none');
			});
            $('#picture_file').change(function () {
                var url = URL.createObjectURL(this.files[0]);

                if (-1 !== this.files[0].type.indexOf('image/')) {
                    var $picture = $('#picture');

                    $picture.attr('src', 'assets/square.png');
                    $picture.css('background-image', 'url(' + url + ')');
                }
            });
		</script>
		</body>
		</html>
        <?php
    }

    /**
     * Populate data for the form
	 *
	 * @return array
     */
    private function populate()
    {
        $data = [
            'username' => '',
            'job' => '',
            'job2' => '',
            'picture' => null
        ];
        if (isset($_POST['username'])) {
            $data['username'] = trim($_POST['username']);
        }
        if (isset($_POST['job'])) {
            $data['job'] = trim($_POST['job']);
        }
        if (isset($_POST['job2'])) {
            $data['job2'] = trim($_POST['job2']);
        }
        if (isset($_FILES) && isset($_FILES['picture'])) {
            $data['picture'] = $_FILES['picture']['tmp_name'];
        }

        return $data;
    }

    /**
     * Run the script
     */
    public function run()
    {
        $data = $this->populate();
        $error = null;
        if (isset($_POST['submit']) && 'save' === $_POST['submit']) {
			try {
				$this->manageImage($data['username'], $data['job'], $data['job2'], $data['picture']);
			}
			catch (\Exception $e) {
				$error = $e->getMessage();
			}
        }
        $this->displayForm($data, $error);
    }
}

$o = new Coworking();
$o->run();
