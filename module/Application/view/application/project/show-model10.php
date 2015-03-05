<?php

//phpinfo();
/** @var $user \Application\Entity\User */
$user = $this->user;

/** @var $project \Application\Entity\Project */
$project = $this->project;

/** @var $designers \Application\Entity\User[] */
//$designers = $this->designers;

$userPeNumber=$this->userPeNumber;
					
foreach($project->getListPe() as $pe)
          $cubes_tmp[]=array('peNumber'=>$pe->getNumber(),'x'=>$pe->getKx(),'y'=>$pe->getKy()
				,'z'=>$pe->getKz(),'dx'=>$pe->getDx(),'dy'=>$pe->getDy(),'dz'=>$pe->getDz());

foreach($cubes_tmp as $p)
							foreach($cubes_tmp as $_p)
		$distances[]=sqrt(($p['x']-$_p['x'])*($p['x']-$_p['x'])+($p['y']-$_p['y'])*($p['y']-$_p['y'])+($p['z']-$_p['z'])*($p['z']-$_p['z']));
		$distance=max($distances);
			
						foreach($cubes_tmp as $p){
							foreach($cubes_tmp as $_p){
		if($distance==sqrt(($p['x']-$_p['x'])*($p['x']-$_p['x'])+($p['y']-$_p['y'])*($p['y']-$_p['y'])+($p['z']-$_p['z'])*($p['z']-$_p['z'])))
		{
		$dx=$p['x']-$_p['x'];
		$dy=$p['y']-$_p['y'];
		$dz=$p['z']-$_p['z'];
		}
		}
		}
		//echo "dx=$dx,dy=$dy,dz=$dz";
		
		$distance = $distance/1.5;
		
foreach($project->getListPe() as $pe)
  $cubes[]=array('peNumber'=>$pe->getNumber(),
  'x'=> round(($pe->getKx()-$dx)/$distance , 1),'y'=> round(($pe->getKy()-$dy)/$distance , 1),
'z'=> round(($pe->getKz()-$dz)/$distance , 1),'dx'=> round($pe->getDx()/$distance , 1),
'dy'=> round($pe->getDy()/$distance , 1),'dz'=> round($pe->getDz()/$distance , 1),
	'img'=>'img/'.$pe->getNumber().'.png'); 

//unset( $cubes[1]);unset($cubes[2]);unset($cubes[3]);
//echo "<pre>"; print_r($cubes);
$dir = dirname(dirname(dirname(dirname(dirname(__DIR__))))).'\public';
echo $dir;
chdir($dir);

 	
				
		//Отрисовка изображений
foreach($cubes as $obj){
$im = imagecreate(20, 20);

// Белый фон, синий текст
$bg = imagecolorallocate($im, 255, 0, 0);
$textcolor = imagecolorallocate($im, 0, 0, 0);

// Надпись в левом верхнем углу
imagestring($im, 20, 5, 2, $obj[peNumber], $textcolor);

//imagepng($im,'1.png');
$im_p = imagecreatetruecolor(128, 128);
imagecopyresampled($im_p, $im, 0, 0, 0, 0, 128, 128, 20, 20);
imagepng($im_p,$obj[img]);
imagedestroy($im);
}?>

<?php //show 3D ?>

<html>
<head>
<title>3D in WebGL!</title>
<meta charset="utf-8" />
</head>
<body>
<canvas id="canvas3D" width="800" height="600">Ваш браузер не поддерживает элемент canvas</canvas>
<script  type="text/javascript" src="/js/gl-matrix-min.js"></script>
 
<script id="shader-fs" type="x-shader/x-fragment">
precision highp float;
uniform sampler2D uSampler;
varying vec2 vTextureCoords;
 
  void main(void) {
    gl_FragColor = texture2D(uSampler, vTextureCoords);
  }
</script>
 
<script id="shader-vs" type="x-shader/x-vertex">
attribute vec3 aVertexPosition;
attribute vec2 aVertexTextureCoords;
varying vec2 vTextureCoords;
uniform mat4 uMVMatrix;
  uniform mat4 uPMatrix;
  void main(void) {
    gl_Position = uPMatrix * uMVMatrix * vec4(aVertexPosition, 1.0);
    vTextureCoords = aVertexTextureCoords;
     
  }
</script>
 
<script type="text/javascript">

<?php foreach($cubes as $cube): ?>
var X<?php echo $cube['peNumber'];?>VertexBuffer;
var X<?php echo $cube['peNumber'];?>IndexBuffer;
var X<?php echo $cube['peNumber'];?>TextureCoordsBuffer;
var X<?php echo $cube['peNumber'];?>Texture;
<?php endforeach;?>

var gl;
var shaderProgram;
 
 // переменная для хранения текстуры кирпичной стены
var angle = 0.0; //угол вращения в радианах
var angle2 = 0.0; //угол вращения в радианах
var zTranslation = -2.0; // смещение по оси Z
 
var mvMatrix = mat4.create(); 
var pMatrix = mat4.create();
 
// установка шейдеров
function initShaders() {
    var fragmentShader = getShader(gl.FRAGMENT_SHADER, 'shader-fs');
    var vertexShader = getShader(gl.VERTEX_SHADER, 'shader-vs');
 
    shaderProgram = gl.createProgram();
 
    gl.attachShader(shaderProgram, vertexShader);
    gl.attachShader(shaderProgram, fragmentShader);
 
    gl.linkProgram(shaderProgram);
      
    if (!gl.getProgramParameter(shaderProgram, gl.LINK_STATUS)) {
        alert("Не удалось установить шейдеры");
    }
      
    gl.useProgram(shaderProgram);
 
    shaderProgram.vertexPositionAttribute = gl.getAttribLocation(shaderProgram, "aVertexPosition"); 
    gl.enableVertexAttribArray(shaderProgram.vertexPositionAttribute);
     
    shaderProgram.vertexTextureAttribute = gl.getAttribLocation(shaderProgram, "aVertexTextureCoords");
    gl.enableVertexAttribArray(shaderProgram.vertexTextureAttribute);
     
    shaderProgram.MVMatrix = gl.getUniformLocation(shaderProgram, "uMVMatrix");
    shaderProgram.ProjMatrix = gl.getUniformLocation(shaderProgram, "uPMatrix");
}
function setMatrixUniforms(){
    gl.uniformMatrix4fv(shaderProgram.ProjMatrix,false, pMatrix);
    gl.uniformMatrix4fv(shaderProgram.MVMatrix, false, mvMatrix);  
} 
// Функция создания шейдера
function getShader(type,id) {
    var source = document.getElementById(id).innerHTML;
 
    var shader = gl.createShader(type);
     
    gl.shaderSource(shader, source);
 
    gl.compileShader(shader);
   
    if (!gl.getShaderParameter(shader, gl.COMPILE_STATUS)) {
        alert("Ошибка компиляции шейдера: " + gl.getShaderInfoLog(shader));
        gl.deleteShader(shader);   
        return null;
    }
    return shader;  
}

<?php foreach($cubes as $cube): ?>

function initX<?php echo $cube['peNumber'];?>Buffers() {
 
    var vertices =[
// лицевая часть
<?php echo $cube['x']- $cube['dx'] ; ?>,<?php echo $cube['y']- $cube['dy'] ; ?>,<?php echo $cube['z']- $cube['dz'] ; ?>,
<?php echo $cube['x']+ $cube['dx'] ; ?>,<?php echo $cube['y']- $cube['dy'] ; ?>,<?php echo $cube['z']- $cube['dz'] ; ?>,
<?php echo $cube['x']+ $cube['dx'] ; ?>,<?php echo $cube['y']+ $cube['dy'] ; ?>,<?php echo $cube['z']- $cube['dz'] ; ?>,
<?php echo $cube['x']- $cube['dx'] ; ?>,<?php echo $cube['y']+ $cube['dy'] ; ?>,<?php echo $cube['z']- $cube['dz'] ; ?>,
// задняя часть
<?php echo $cube['x']- $cube['dx'] ; ?>,<?php echo $cube['y']- $cube['dy'] ; ?>,<?php echo $cube['z']+ $cube['dz'] ; ?>,
<?php echo $cube['x']- $cube['dx'] ; ?>,<?php echo $cube['y']+ $cube['dy'] ; ?>,<?php echo $cube['z']+ $cube['dz'] ; ?>,
<?php echo $cube['x']+ $cube['dx'] ; ?>,<?php echo $cube['y']+ $cube['dy'] ; ?>,<?php echo $cube['z']+ $cube['dz'] ; ?>,
<?php echo $cube['x']+ $cube['dx'] ; ?>,<?php echo $cube['y']- $cube['dy'] ; ?>,<?php echo $cube['z']+ $cube['dz'] ; ?>,
//левая боковая часть
<?php echo $cube['x']- $cube['dx'] ; ?>,<?php echo $cube['y']- $cube['dy'] ; ?>,<?php echo $cube['z']- $cube['dz'] ; ?>,
<?php echo $cube['x']- $cube['dx'] ; ?>,<?php echo $cube['y']+ $cube['dy'] ; ?>,<?php echo $cube['z']- $cube['dz'] ; ?>,
<?php echo $cube['x']- $cube['dx'] ; ?>,<?php echo $cube['y']+ $cube['dy'] ; ?>,<?php echo $cube['z']+ $cube['dz'] ; ?>,
<?php echo $cube['x']- $cube['dx'] ; ?>,<?php echo $cube['y']- $cube['dy'] ; ?>,<?php echo $cube['z']+ $cube['dz'] ; ?>,
// правая боковая часть
<?php echo $cube['x']+ $cube['dx'] ; ?>,<?php echo $cube['y']- $cube['dy'] ; ?>,<?php echo $cube['z']- $cube['dz'] ; ?>,
<?php echo $cube['x']+ $cube['dx'] ; ?>,<?php echo $cube['y']- $cube['dy'] ; ?>,<?php echo $cube['z']+ $cube['dz'] ; ?>,
<?php echo $cube['x']+ $cube['dx'] ; ?>,<?php echo $cube['y']+ $cube['dy'] ; ?>,<?php echo $cube['z']+ $cube['dz'] ; ?>,
<?php echo $cube['x']+ $cube['dx'] ; ?>,<?php echo $cube['y']+ $cube['dy'] ; ?>,<?php echo $cube['z']- $cube['dz'] ; ?>,
// низ
<?php echo $cube['x']- $cube['dx'] ; ?>,<?php echo $cube['y']- $cube['dy'] ; ?>,<?php echo $cube['z']- $cube['dz'] ; ?>,
<?php echo $cube['x']+ $cube['dx'] ; ?>,<?php echo $cube['y']- $cube['dy'] ; ?>,<?php echo $cube['z']- $cube['dz'] ; ?>,
<?php echo $cube['x']+ $cube['dx'] ; ?>,<?php echo $cube['y']- $cube['dy'] ; ?>,<?php echo $cube['z']+ $cube['dz'] ; ?>,
<?php echo $cube['x']- $cube['dx'] ; ?>,<?php echo $cube['y']- $cube['dy'] ; ?>,<?php echo $cube['z']+ $cube['dz'] ; ?>,
// верх
<?php echo $cube['x']- $cube['dx'] ; ?>,<?php echo $cube['y']+ $cube['dy'] ; ?>,<?php echo $cube['z']- $cube['dz'] ; ?>,
<?php echo $cube['x']+ $cube['dx'] ; ?>,<?php echo $cube['y']+ $cube['dy'] ; ?>,<?php echo $cube['z']- $cube['dz'] ; ?>,
<?php echo $cube['x']+ $cube['dx'] ; ?>,<?php echo $cube['y']+ $cube['dy'] ; ?>,<?php echo $cube['z']+ $cube['dz'] ; ?>,
<?php echo $cube['x']- $cube['dx'] ; ?>,<?php echo $cube['y']+ $cube['dy'] ; ?>,<?php echo $cube['z']+ $cube['dz'] ; ?>,
];
                  
    var indices = [ // лицевая часть
                0, 1, 2, 
                2, 3, 0,
                // задняя часть
                4, 5, 6,
                6, 7, 4,
                //левая боковая часть
                8, 9, 10, 
                10, 11, 8,
                // правая боковая часть
                12, 13, 14, 
                14, 15, 12,
                // низ
                16, 17, 18, 
                18, 19, 16,
                // верх
                20, 21, 22, 
                22, 23, 20
				];
 
    X<?php echo $cube['peNumber'];?>VertexBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, X<?php echo $cube['peNumber'];?>VertexBuffer);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(vertices), gl.STATIC_DRAW);
    X<?php echo $cube['peNumber'];?>VertexBuffer.itemSize = 3;
 
    X<?php echo $cube['peNumber'];?>IndexBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, X<?php echo $cube['peNumber'];?>IndexBuffer);
    gl.bufferData(gl.ELEMENT_ARRAY_BUFFER, new Uint16Array(indices), gl.STATIC_DRAW);
    X<?php echo $cube['peNumber'];?>IndexBuffer.numberOfItems = indices.length; 
   
  // Координаты текстуры
  var textureCoords = [
                0.0, 0.0,
                0.0, 1.0,
                1.0, 1.0,
                1.0, 0.0,
                 
                0.0, 0.0,
                0.0, 1.0,
                1.0, 1.0,
                1.0, 0.0,
                 
                0.0, 0.0,
                0.0, 1.0,
                1.0, 1.0,
                1.0, 0.0,
                 
                0.0, 0.0,
                0.0, 1.0,
                1.0, 1.0,
                1.0, 0.0,
				
				0.0, 0.0,
                0.0, 1.0,
                1.0, 1.0,
                1.0, 0.0,
                 
                0.0, 0.0,
                0.0, 1.0,
                1.0, 1.0,
                1.0, 0.0
  ];

    // Создание буфера координат текстуры
    X<?php echo $cube['peNumber'];?>TextureCoordsBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, X<?php echo $cube['peNumber'];?>TextureCoordsBuffer);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(textureCoords), gl.STATIC_DRAW);
    X<?php echo $cube['peNumber'];?>TextureCoordsBuffer.itemSize=2;
}

function X<?php echo $cube['peNumber'];?>Draw() {    
 
    gl.bindBuffer(gl.ARRAY_BUFFER, X<?php echo $cube['peNumber'];?>VertexBuffer);
    gl.vertexAttribPointer(shaderProgram.vertexPositionAttribute, 
                         X<?php echo $cube['peNumber'];?>VertexBuffer.itemSize, gl.FLOAT, false, 0, 0);
     
 
    gl.bindBuffer(gl.ARRAY_BUFFER, X<?php echo $cube['peNumber'];?>TextureCoordsBuffer);
    gl.vertexAttribPointer(shaderProgram.vertexTextureAttribute,
                         X<?php echo $cube['peNumber'];?>TextureCoordsBuffer.itemSize, gl.FLOAT, false, 0, 0);
    gl.activeTexture(gl.TEXTURE0);
    gl.bindTexture(gl.TEXTURE_2D, X<?php echo $cube['peNumber'];?>Texture);
    gl.enable(gl.DEPTH_TEST);
    gl.drawElements(gl.TRIANGLES, X<?php echo $cube['peNumber'];?>IndexBuffer.numberOfItems, gl.UNSIGNED_SHORT,0);
}

<?php endforeach; ?>

function setupWebGL()
{
    gl.clearColor(1.0, 1.0, 1.0, 1.0);  
    gl.clear(gl.COLOR_BUFFER_BIT || gl.DEPTH_BUFFER_BIT); 
     
    gl.viewport(0, 0, gl.viewportWidth, gl.viewportHeight);
    mat4.perspective(pMatrix, 1.04, gl.viewportWidth / gl.viewportHeight, 0.1, 100.0);
    mat4.identity(mvMatrix);
    mat4.translate(mvMatrix,mvMatrix,[0, 0, zTranslation]);
    mat4.rotate(mvMatrix,mvMatrix, angle, [0, 1, 0]);
	mat4.rotate(mvMatrix,mvMatrix, angle2, [1, 0, 0]);
}
 function setupTextures() {
	
	<?php foreach($cubes as $cube): ?>
	
    X<?php echo $cube['peNumber'];?>Texture = gl.createTexture();
    setTexture("/<?php echo $cube['img'];?>", X<?php echo $cube['peNumber'];?>Texture);
     
    <?php endforeach; ?>
 }
function setTexture(url, texture){
 
    gl.bindTexture(gl.TEXTURE_2D, texture);
    var image = new Image();
    image.onload = function() {
     
        handleTextureLoaded(image, texture);    
  }
   
   image.src = url;
 
    shaderProgram.samplerUniform = gl.getUniformLocation(shaderProgram, "uSampler");
    gl.uniform1i(shaderProgram.samplerUniform, 0);
}
 function handleTextureLoaded(image, texture) {
 
    gl.bindTexture(gl.TEXTURE_2D, texture);
    gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, true);
    gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, gl.RGBA, gl.UNSIGNED_BYTE, image);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.LINEAR);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR);
    gl.bindTexture(gl.TEXTURE_2D, null);
}
window.onload=function(){
 
    var canvas = document.getElementById("canvas3D");
    try {
        gl = canvas.getContext("webgl") || canvas.getContext("experimental-webgl");
    }
    catch(e) {}
   
      if (!gl) {
        alert("Ваш браузер не поддерживает WebGL");
      }
    if(gl){
        document.addEventListener('keydown', handleKeyDown, false);
        gl.viewportWidth = canvas.width;
        gl.viewportHeight = canvas.height;
 
        initShaders();
		
		<?php foreach($cubes as $cube): ?>
		initX<?php echo $cube['peNumber'];?>Buffers();       
		<?php endforeach; ?>
		
        setupTextures();
        (function animloop(){
             
            setupWebGL();
            setMatrixUniforms();
			
			<?php foreach($cubes as $cube): ?>
            X<?php echo $cube['peNumber'];?>Draw();
            <?php endforeach; ?>
			
            requestAnimFrame(animloop, canvas);
        })();
    }
}
function handleKeyDown(e){
    switch(e.keyCode)
    {
        case 39:  
            angle+=0.1;
            break;
        case 37: 
            angle-=0.1;
            break;
		case 40:  
            angle2+=0.1;
            break;
        case 38: 
            angle2-=0.1;
            break;
        case 34: 
            zTranslation+=0.1;
            break;
        case 33: 
            zTranslation-=0.1;
            break;
    }
}
window.requestAnimFrame = (function(){
      return  window.requestAnimationFrame       || 
              window.webkitRequestAnimationFrame || 
              window.mozRequestAnimationFrame    || 
              window.oRequestAnimationFrame      || 
              window.msRequestAnimationFrame     ||
         function(callback, element) {
           return window.setTimeout(callback, 1000/60);
         };
 
})();
</script>
</body>
</html>
