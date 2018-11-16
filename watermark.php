<?php

if(isset($_FILES['image_file']))
{
	$max_size = 800; //Tamanho Max. em Pixel da imagem
	$destination_folder = '/';
	$watermark_png_file = 'watermark.png'; //Endereço da WaterMask
	
	$image_name = $_FILES['image_file']['name'];
	$image_size = $_FILES['image_file']['size'];
	$image_temp = $_FILES['image_file']['tmp_name'];
	$image_type = $_FILES['image_file']['type'];

	switch(strtolower($image_type)){ // Determina o tipo da imagem
			//Cria uma nova imagem
			case 'image/png': 
				$image_resource =  imagecreatefrompng($image_temp);
				break;
			case 'image/gif':
				$image_resource =  imagecreatefromgif($image_temp);
				break;          
			case 'image/jpeg': case 'image/pjpeg':
				$image_resource = imagecreatefromjpeg($image_temp);
				break;
			default:
				$image_resource = false;
		}
	
	if($image_resource){
		//Copie e redimensiona parte de uma imagem com nova resolução
		list($img_width, $img_height) = getimagesize($image_temp);
		
	    //Constrói um tamanho proporcional de nova imagem
		$image_scale        = min($max_size / $img_width, $max_size / $img_height); 
		$new_image_width    = ceil($image_scale * $img_width);
		$new_image_height   = ceil($image_scale * $img_height);
		$new_canvas         = imagecreatetruecolor($new_image_width , $new_image_height);
		$padding = 5;

		if(imagecopyresampled($new_canvas, $image_resource , 0, 0, 0, 0, $new_image_width, $new_image_height, $img_width, $img_height))
		{
			
			if(!is_dir($destination_folder)){ 
				mkdir($destination_folder);//Cria o diretório caso não exista
			}


			$watermark = imagecreatefrompng($watermark_png_file); //Imagem da WaterMask

			// Posição da WaterMask
			$dest_x = $new_image_width - imagesx($watermark) - $padding;
			$dest_y = $new_image_height - imagesy($watermark) - $padding;

			imagecopy($new_canvas, $watermark, $dest_x, $dest_y, 0, 0, imagesx($watermark), imagesy($watermark)); //merge image
			
			//Saída de imagem diretamente no navegador.
			header('Content-Type: image/jpeg');
			imagejpeg($new_canvas, NULL , 90);
			
			//Caso queira salvar a imagem em algum diretório descomente o código abaixo.
			//imagejpeg($new_canvas, $destination_folder.'/'.$image_name , 90);
			
			//Libera a memoria
			imagedestroy($new_canvas); 
			imagedestroy($image_resource);
			die();
		}
	}
}

?>
<!DOCTYPE HTML>
<html>
<head>
<style type="text/css">
#upload-form {
	padding: 20px;
	background: #F7F7F7;
	border: 1px solid #CCC;
	margin-left: auto;
	margin-right: auto;
	width: 400px;
}
#upload-form input[type=file] {
	border: 1px solid #ddd;
	padding: 4px;
}
#upload-form input[type=submit] {
	height: 30px;
}
</style>
</head>
<body>

<form action="" id="upload-form" method="post" enctype="multipart/form-data">
<input type="file" name="image_file" />
<input type="submit" value="Enviar imagem" />
</form>

</body>
</html>