<?php
/**
*	ImageHandler - ResizeToDimension()
*
* 	Resizes an image to fit into a specifie dimension
*
* 	EXAMPLE USAGE:
*
* 	$ImageHandler->ResizeToDimension(200, "file.jpg", "png", "images");
*
*	@param	int			$dimension - dimension to fit into
*	@param	string		$source - image source
*	@param	string		$extension - image source file type
*	@param	string		$destination - destination directory
*
*/

function ResizeToDimension($dimension, $source, $extension, $destination)
{

	//get the image size
	$size = getimagesize($source);

	//determine dimensions
	$width = $size[0];
	$height = $size[1];

	//determine what the file extension of the source
	//image is
	//echo '~~'.$extension.'**'.$source.'~~';
	switch($extension)
	{

		//its a gif
		case 'gif': case 'GIF':
			//create a gif from the source
			$sourceImage = imagecreatefromgif($source);
			break;
		case 'jpg': case 'JPG': case 'jpeg':
			//create a jpg from the source
			$sourceImage = imagecreatefromjpeg($source);
			break;
		case 'png': case 'PNG':
			//create a png from the source
			$sourceImage = imagecreatefrompng($source);
			break;
		default:
			//create a png from the source
			$sourceImage = imagecreatefrompng($source);
			break;	

	}

	// find the largest dimension of the image
	// then calculate the resize perc based upon that dimension
	$percentage = ( $width >= $height ) ? 100 / $width * $dimension : 100 / $height * $dimension;

	// define new width / height
	$newWidth = $width / 100 * $percentage;
	$newHeight = $height / 100 * $percentage;

	// create a new image
	$destinationImage = imagecreatetruecolor($newWidth, $newHeight);

	// copy resampled
	imagecopyresampled($destinationImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    //exif only supports jpg in our supported file types
    if ($extension == "jpg" || $extension == "jpeg")
	{

		//fix photos taken on cameras that have incorrect
		//dimensions
		$exif = exif_read_data($source);

		//get the orientation
		$ort = $exif['Orientation'];

		//determine what oreientation the image was taken at
		switch($ort)
	    {

	        case 2: // horizontal flip

	            $this->ImageFlip($dimg);

	        	break;

	        case 3: // 180 rotate left

	            $destinationImage = imagerotate($destinationImage, 180, -1);

	        	break;

	        case 4: // vertical flip

	            $this->ImageFlip($dimg);

	       		break;

	        case 5: // vertical flip + 90 rotate right

	            $this->ImageFlip($destinationImage);

	            $destinationImage = imagerotate($destinationImage, -90, -1);

	        	break;

	        case 6: // 90 rotate right

	            $destinationImage = imagerotate($destinationImage, -90, -1);

	        	break;

	        case 7: // horizontal flip + 90 rotate right

	            $this->ImageFlip($destinationImage);

	            $destinationImage = imagerotate($destinationImage, -90, -1);

	        	break;

	        case 8: // 90 rotate left

	            $destinationImage = imagerotate($destinationImage, 90, -1);

	        	break;

	    }

	}

	// create the jpeg
	return imagejpeg($destinationImage, $destination, 100);

}

















/**
*	ImageHandler - ImageFlip()
*
* 	Resizes an image to set width and height
*
* 	EXAMPLE USAGE:
*
* 	$ImageHandler->Resize(200, "file.jpg", "png", "images");
*
*	@param	string		$image (image to flip)
*	@param	int			$x
*	@param	int			$y
*	@param	int			$width
*	@param	int			$height
*
*/

function ImageFlip(&$image, $x = 0, $y = 0, $width = null, $height = null)
{

    if ($width  < 1) $width  = imagesx($image);
    if ($height < 1) $height = imagesy($image);

    // Truecolor provides better results, if possible.
    if (function_exists('imageistruecolor') && imageistruecolor($image))
    {

        $tmp = imagecreatetruecolor(1, $height);

    }
    else
    {

        $tmp = imagecreate(1, $height);

    }

    $x2 = $x + $width - 1;

    for ($i = (int)floor(($width - 1) / 2); $i >= 0; $i--)
    {

        // Backup right stripe.
        imagecopy($tmp, $image, 0, 0, $x2 - $i, $y, 1, $height);

        // Copy left stripe to the right.
        imagecopy($image, $image, $x2 - $i, $y, $x + $i, $y, 1, $height);

        // Copy backuped right stripe to the left.
        imagecopy($image, $tmp, $x + $i,  $y, 0, 0, 1, $height);

    }

    imagedestroy($tmp);

    return true;

}
?>