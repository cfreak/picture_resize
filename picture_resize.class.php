<?php
class picture_resize
{
	var $img;

	function __construct($imgfile)
	{
		//detect image format
		//$this->img["format"]=preg_replace("/.*\.(.*)$/","\\1",$imgfile);
		$size=getimagesize($imgfile);
		$origwidth=$size[0];
		$origheight=$size[1];
		$imgType=$size[2];
		/*
		$types = array(
        1 => 'GIF',
        2 => 'JPG',
        3 => 'PNG',
        4 => 'SWF',
        5 => 'PSD',
        6 => 'BMP',
        7 => 'TIFF(intel byte order)',
        8 => 'TIFF(motorola byte order)',
        9 => 'JPC',
        10 => 'JP2',
        11 => 'JPX',
        12 => 'JB2',
        13 => 'SWC',
        14 => 'IFF',
        15 => 'WBMP',
        16 => 'XBM'
   		 ); */
		if($imgType==1){
			$this->img["format"] = "GIF";
		}
		if($imgType==2){
			$this->img["format"] = "JPEG";
		}
		if($imgType==3){
			$this->img["format"] = "PNG";
		}
		if($imgType==15){
			$this->img["format"] = "WBMP";
		}
		
		$this->img["format"]=strtoupper($this->img["format"]);
		if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
			//JPEG
			$this->img["format"]="JPEG";
			$this->img["src"] = ImageCreateFromJPEG ($imgfile);
		} elseif ($this->img["format"]=="PNG") {
			//PNG
			$this->img["format"]="PNG";
			$this->img["src"] = ImageCreateFromPNG ($imgfile);
		} elseif ($this->img["format"]=="GIF") {
			//GIF
			$this->img["format"]="GIF";
			$this->img["src"] = ImageCreateFromGIF ($imgfile);
		} elseif ($this->img["format"]=="WBMP") {
			//WBMP
			$this->img["format"]="WBMP";
			$this->img["src"] = ImageCreateFromWBMP ($imgfile);
		} else {
			//DEFAULT
			echo "Not Supported File";
			exit();
		}
		@$this->img["lebar"] = imagesx($this->img["src"]);
		@$this->img["tinggi"] = imagesy($this->img["src"]);
		//default quality jpeg
		$this->img["quality"]=75;
	}

	function size_height($size=100)
	{
		//height
    	$this->img["tinggi_thumb"]=$size;
    	@$this->img["lebar_thumb"] = ($this->img["tinggi_thumb"]/$this->img["tinggi"])*$this->img["lebar"];
	}

	function size_width($size=100)
	{
		//width
		$this->img["lebar_thumb"]=$size;
    	@$this->img["tinggi_thumb"] = ($this->img["lebar_thumb"]/$this->img["lebar"])*$this->img["tinggi"];
	}

	function size_auto($size=100)
	{
		//size
		if ($this->img["lebar"]>=$this->img["tinggi"]) {
    		$this->img["lebar_thumb"]=$size;
    		@$this->img["tinggi_thumb"] = ($this->img["lebar_thumb"]/$this->img["lebar"])*$this->img["tinggi"];
		} else {
	    	$this->img["tinggi_thumb"]=$size;
    		@$this->img["lebar_thumb"] = ($this->img["tinggi_thumb"]/$this->img["tinggi"])*$this->img["lebar"];
 		}
	}

	function jpeg_quality($quality=75)
	{
		//jpeg quality
		$this->img["quality"]=$quality;
	}
	
	function imagecopyresampled_adv($image_type)
    {
	      switch ($image_type)
	      {
	
	        case "GIF":
	          $transcol=imagecolortransparent($this->img["src"]);
	          $this->img["des"] = imagecreate($this->img["lebar_thumb"],$this->img["tinggi_thumb"]);
	          imagepalettecopy($this->img["des"], $this->img["src"]);
	          imagefill($this->img["des"], 0, 0, $transcol);
	          imagecolortransparent($this->img["des"], $transcol);
	          return imagecopyresampled($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["lebar"], $this->img["tinggi"]);
	        break;
	
	        case "PNG":
	          $this->img["des"] = imageCreateTrueColor($this->img["lebar_thumb"],$this->img["tinggi_thumb"]);
	          imagealphablending($this->img["des"], false);
	          imagesavealpha($this->img["des"],true);
	          $transparent = imagecolorallocatealpha($this->img["des"], 255, 255, 255, 0);
	          imagecolortransparent($this->img["des"],$transparent);
	
	          return imagecopyresampled($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["lebar"], $this->img["tinggi"]);
	        break;
	       
	        default: 
	          $this->img["des"] = imageCreateTrueColor($this->img["lebar_thumb"],$this->img["tinggi_thumb"]);
	          return imagecopyresampled($this->img["des"], $this->img["src"], 0, 0, 0, 0, $this->img["lebar_thumb"], $this->img["tinggi_thumb"], $this->img["lebar"], $this->img["tinggi"]);
	      }
    }

	function show()
	{
		//show thumb
		@Header("Content-Type: image/".$this->img["format"]);

		$this->imagecopyresampled_adv ($this->img["format"]);

		if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
			//JPEG
			imageJPEG($this->img["des"],"",$this->img["quality"]);
		} elseif ($this->img["format"]=="PNG") {
			//PNG
			imagePNG($this->img["des"]);
		} elseif ($this->img["format"]=="GIF") {
			//GIF
			imageGIF($this->img["des"]);
		} elseif ($this->img["format"]=="WBMP") {
			//WBMP
			imageWBMP($this->img["des"]);
		}
	}

	function save($save="")
	{
		//save thumb
		if (empty($save)) {
			$save=strtolower("./thumb.".$this->img["format"]);
		}
		
    	$this->imagecopyresampled_adv ($this->img["format"]);
    	
		if ($this->img["format"]=="JPG" || $this->img["format"]=="JPEG") {
			//JPEG
			imageJPEG($this->img["des"],"$save",$this->img["quality"]);
		} elseif ($this->img["format"]=="PNG") {
			//PNG
			imagePNG($this->img["des"],"$save");
		} elseif ($this->img["format"]=="GIF") {
			//GIF
			imageGIF($this->img["des"],"$save");
		} elseif ($this->img["format"]=="WBMP") {
			//WBMP
			imageWBMP($this->img["des"],"$save");
		}
	}
}
?>