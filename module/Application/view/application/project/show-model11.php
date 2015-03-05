<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Диплом</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Le styles -->
        <link href="/css/bootstrap.min.css" media="screen" rel="stylesheet" type="text/css">
<link href="/css/bootstrap-theme.min.css" media="screen" rel="stylesheet" type="text/css">
<link href="/css/style.css" media="screen" rel="stylesheet" type="text/css">
<link href="/img/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon">
        <!-- Scripts -->
        <!--[if lt IE 9]><script type="text/javascript" src="/js/html5shiv.js"></script><![endif]-->
<!--[if lt IE 9]><script type="text/javascript" src="/js/respond.min.js"></script><![endif]-->
<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript" src="/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/js/bootstrap.file-input.js"></script>
    </head>
    <body>


<?php

$lines=$this->lines;
$points=$this->points;
$hands=$this->hands;
echo "<pre>";
print_r($points);
print_r($lines);
print_r($hands);
die();
?>

<html>
<head>
<title>3D in WebGL!</title>
<meta charset="utf-8" />
</head>
<body>
<canvas id="canvas3D" width="800" height="600">Ваш браузер не поддерживает элемент canvas</canvas>
<script  type="text/javascript" src="/js/gl-matrix-min.js"></script>
 
<script id="shader-vs" type="x-shader/x-vertex">
  attribute vec3 aVertexPosition;
  attribute vec3 aVertexNormal;
  attribute vec2 aVertexTextureCoords;
 
  uniform mat4 uMVMatrix;
  uniform mat4 uPMatrix;
  uniform mat3 uNMatrix;
   
  uniform vec3 uLightPosition;
  uniform vec3 uAmbientLightColor;
  uniform vec3 uDiffuseLightColor;
  uniform vec3 uSpecularLightColor;
   
  varying vec2 vTextureCoords;
  varying vec3 vLightWeighting;
   
  const float shininess = 16.0;
     
  void main() {
    // установка позиции наблюдателя сцены
    vec4 vertexPositionEye4 = uMVMatrix * vec4(aVertexPosition, 1.0);
    vec3 vertexPositionEye3 = vertexPositionEye4.xyz / vertexPositionEye4.w;
   
    // получаем вектор направления света
    vec3 lightDirection = normalize(uLightPosition - vertexPositionEye3);
     
    // получаем нормаль
    vec3 normal = normalize(uNMatrix * aVertexNormal);
     
    // получаем скалярное произведение векторов нормали и направления света
    float diffuseLightDot = max(dot(normal, lightDirection), 0.0);
                                        
    // получаем вектор отраженного луча и нормализуем его
    vec3 reflectionVector = normalize(reflect(-lightDirection, normal));
     
    // установка вектора камеры
    vec3 viewVectorEye = -normalize(vertexPositionEye3);
     
    float specularLightDot = max(dot(reflectionVector, viewVectorEye), 0.0);
     
    float specularLightParam = pow(specularLightDot, shininess);
 
    // отраженный свет равен сумме фонового, диффузного и зеркального отражений света
    vLightWeighting = uAmbientLightColor + uDiffuseLightColor * diffuseLightDot +
                      uSpecularLightColor * specularLightParam;
     
     // Finally transform the geometry
     gl_Position = uPMatrix * uMVMatrix * vec4(aVertexPosition, 1.0);
     vTextureCoords = aVertexTextureCoords;  
  }                
</script>
 
<script id="shader-fs" type="x-shader/x-fragment">
  precision mediump float;
   
  varying vec2 vTextureCoords;
  varying vec3 vLightWeighting;
  uniform sampler2D uSampler;
   
  void main() {   
    vec4 texelColor = texture2D(uSampler, vTextureCoords);
    gl_FragColor = vec4(vLightWeighting.rgb * texelColor.rgb, texelColor.a);
  } 
</script>
 
<script type="text/javascript">

<?php foreach($hands as $cube): ?>
var X<?php echo $cube['number'];?>VertexBuffer;
var X<?php echo $cube['number'];?>IndexBuffer;
var X<?php echo $cube['number'];?>TextureCoordsBuffer;
var X<?php echo $cube['number'];?>Texture;
<?php endforeach;?>


var LVertexBuffer;
var LIndexBuffer;

var gl;
var shaderProgram;
 
 // переменная для хранения текстуры кирпичной стены
var angle = 0.0; //угол вращения в радианах
var angle2 = 0.0; //угол вращения в радианах
var angle3 = 0.0; //угол вращения в радианах

var zTranslation = -2.0; // смещение по оси Z
var xTranslation = 0.0;
var yTranslation = 0.0;

var mvMatrix = mat4.create(); 
var pMatrix = mat4.create();
var nMatrix = mat3.create();  // матрица нормалей
 
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
     
    // атрибут нормали
    shaderProgram.vertexNormalAttribute = gl.getAttribLocation(shaderProgram, "aVertexNormal");
    gl.enableVertexAttribArray(shaderProgram.vertexNormalAttribute);
     
    // настройка параметров uniform матриц для передачи в шейдер
    shaderProgram.MVMatrix = gl.getUniformLocation(shaderProgram, "uMVMatrix");
    shaderProgram.ProjMatrix = gl.getUniformLocation(shaderProgram, "uPMatrix");
    shaderProgram.NormalMatrix = gl.getUniformLocation(shaderProgram, "uNMatrix"); 
     
    // настройка переменных uniform освещения для передачи в шейдер
    // позиция источника света
    shaderProgram.uniformLightPosition = gl.getUniformLocation(shaderProgram, "uLightPosition");
    // фоновое отражение света
    shaderProgram.uniformAmbientLightColor = gl.getUniformLocation(shaderProgram, "uAmbientLightColor");  
    // диффузное отражение света
    shaderProgram.uniformDiffuseLightColor = gl.getUniformLocation(shaderProgram, "uDiffuseLightColor");
    // зеркальное отражение света
    shaderProgram.uniformSpecularLightColor = gl.getUniformLocation(shaderProgram, "uSpecularLightColor");
}

function setupLights() {
  //gl.uniform3fv(shaderProgram.uniformLightPosition, [0.0, 10.0, 5.0]);
  gl.uniform3fv(shaderProgram.uniformLightPosition, [0.0, -1.0, -1.0]);
  
/*   gl.uniform3fv(shaderProgram.uniformAmbientLightColor, [0.1, 0.1, 0.1]);
  gl.uniform3fv(shaderProgram.uniformDiffuseLightColor, [0.7, 0.7, 0.7]);
  gl.uniform3fv(shaderProgram.uniformSpecularLightColor, [1.0, 1.0, 1.0]); */
  
  gl.uniform3fv(shaderProgram.uniformAmbientLightColor, [0.3, 0.3, 0.3]);
  gl.uniform3fv(shaderProgram.uniformDiffuseLightColor, [0.7, 0.7, 0.7]);
  gl.uniform3fv(shaderProgram.uniformSpecularLightColor, [1.0, 1.0, 1.0]);
}

function setMatrixUniforms(){
    gl.uniformMatrix4fv(shaderProgram.ProjMatrix,false, pMatrix);
    gl.uniformMatrix4fv(shaderProgram.MVMatrix, false, mvMatrix);
    //  установка матрицы нормалей
    gl.uniformMatrix3fv(shaderProgram.NormalMatrix, false, nMatrix);
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

<?php foreach($hands as $h): ?>

function initX<?php echo $h['number'];?>Buffers() {
 
    var vertices =[
<?php foreach($h[coords] as $p): ?>
<?php echo $p; ?>,
<?php endforeach; ?>
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
 
    X<?php echo $h['number'];?>VertexBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, X<?php echo $h['number'];?>VertexBuffer);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(vertices), gl.STATIC_DRAW);
    X<?php echo $h['number'];?>VertexBuffer.itemSize = 3;
 
    X<?php echo $h['number'];?>IndexBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, X<?php echo $h['number'];?>IndexBuffer);
    gl.bufferData(gl.ELEMENT_ARRAY_BUFFER, new Uint16Array(indices), gl.STATIC_DRAW);
    X<?php echo $h['number'];?>IndexBuffer.numberOfItems = indices.length; 
   
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
    X<?php echo $h['number'];?>TextureCoordsBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, X<?php echo $h['number'];?>TextureCoordsBuffer);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(textureCoords), gl.STATIC_DRAW);
    X<?php echo $h['number'];?>TextureCoordsBuffer.itemSize=2;

	var normals = [
           // Лицевая сторона
           0.0,  0.0,  1.0, //v0
           0.0,  0.0,  1.0, //v1
           0.0,  0.0,  1.0, //v2
           0.0,  0.0,  1.0, //v3
      
           // Задняя сторона
           0.0,  0.0, -1.0, //v4
           0.0,  0.0, -1.0, //v5
           0.0,  0.0, -1.0, //v6
           0.0,  0.0, -1.0, //v7
            
           // Левая боковая сторона
          -1.0,  0.0,  0.0, //v8
          -1.0,  0.0,  0.0, //v9
          -1.0,  0.0,  0.0, //v10
          -1.0,  0.0,  0.0, //v11
            
           // Правая боковая сторона
           1.0,  0.0,  0.0, //v12
           1.0,  0.0,  0.0, //v13
           1.0,  0.0,  0.0, //v14
           1.0,  0.0,  0.0, //v15
		   //низ
		   0.0, -1.0, 0.0,
		   0.0, -1.0, 0.0,
		   0.0, -1.0, 0.0,
		   0.0, -1.0, 0.0,
		   //верх
		   0.0, 1.0, 0.0,
		   0.0, 1.0, 0.0,
		   0.0, 1.0, 0.0,
		   0.0, 1.0, 0.0,
    ];
     
    // Создаем буфер нормалей куба
    X<?php echo $h['number'];?>vertexNormalBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, X<?php echo $h['number'];?>vertexNormalBuffer);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(normals), gl.STATIC_DRAW);
    X<?php echo $h['number'];?>vertexNormalBuffer.itemSize = 3;	
	}

function X<?php echo $h['number'];?>Draw() {    
 
    gl.bindBuffer(gl.ARRAY_BUFFER, X<?php echo $h['number'];?>VertexBuffer);
    gl.vertexAttribPointer(shaderProgram.vertexPositionAttribute, 
                         X<?php echo $h['number'];?>VertexBuffer.itemSize, gl.FLOAT, false, 0, 0);
     
 
    gl.bindBuffer(gl.ARRAY_BUFFER, X<?php echo $h['number'];?>TextureCoordsBuffer);
    gl.vertexAttribPointer(shaderProgram.vertexTextureAttribute,
                         X<?php echo $h['number'];?>TextureCoordsBuffer.itemSize, gl.FLOAT, false, 0, 0);
						 
	gl.bindBuffer(gl.ARRAY_BUFFER, X<?php echo $h['number'];?>vertexNormalBuffer);
    gl.vertexAttribPointer(shaderProgram.vertexNormalAttribute, 
                         X<?php echo $h['number'];?>vertexNormalBuffer.itemSize, gl.FLOAT, false, 0, 0);
						 
    gl.activeTexture(gl.TEXTURE0);
    gl.bindTexture(gl.TEXTURE_2D, X<?php echo $h['number'];?>Texture);
    gl.enable(gl.DEPTH_TEST);
    gl.drawElements(gl.TRIANGLES, X<?php echo $h['number'];?>IndexBuffer.numberOfItems, gl.UNSIGNED_SHORT,0);
}

<?php endforeach; ?>

function initLBuffers() {
 
    var Lvertices =[
<?php foreach($points as $p): ?>
<?php echo $p['x']; ?>,<?php echo $p['y']; ?>,<?php echo $p['z']; ?>,
<?php endforeach; ?>
];
                  
    var Lindices = [ // лицевая часть
 <?php foreach($lines as $p): ?>
<?php echo $p[1]; ?>,<?php echo $p[0]; ?>,
<?php endforeach; ?>
				];
 
    LVertexBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, LVertexBuffer);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(Lvertices), gl.STATIC_DRAW);
    LVertexBuffer.itemSize = 3;
 
    LIndexBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, LIndexBuffer);
    gl.bufferData(gl.ELEMENT_ARRAY_BUFFER, new Uint16Array(Lindices), gl.STATIC_DRAW);
    LIndexBuffer.numberOfItems = Lindices.length; 
   
  // Координаты текстуры
  
	}

function LDraw() {    
 
    gl.bindBuffer(gl.ARRAY_BUFFER, LVertexBuffer);
    gl.vertexAttribPointer(shaderProgram.vertexPositionAttribute, 
                         LVertexBuffer.itemSize, gl.FLOAT, false, 0, 0);
     
    gl.enable(gl.DEPTH_TEST);
    gl.drawElements(gl.LINES, LIndexBuffer.numberOfItems, gl.UNSIGNED_SHORT,0);
}


//конец задания стержней

function setupWebGL()
{
    gl.clearColor(0.9, 1.0, 1.0, 1.0);  
    gl.clear(gl.COLOR_BUFFER_BIT || gl.DEPTH_BUFFER_BIT); 
     
    gl.viewport(0, 0, gl.viewportWidth, gl.viewportHeight);
    mat4.perspective(pMatrix, 1.04, gl.viewportWidth / gl.viewportHeight, 0.1, 100.0);
    mat4.identity(mvMatrix);
    mat4.translate(mvMatrix,mvMatrix,[xTranslation, yTranslation, zTranslation]);
    mat4.rotate(mvMatrix,mvMatrix, angle, [0, 1, 0]);
	mat4.rotate(mvMatrix,mvMatrix, angle2, [1, 0, 0]); 
	mat4.rotate(mvMatrix,mvMatrix, angle3, [0, 0, 1]); 
 
    mat3.normalFromMat4(nMatrix, mvMatrix);
}
 function setupTextures() {
	
	<?php foreach($hands as $h): ?>
	X<?php echo $h['number'];?>Texture = gl.createTexture();
    setTexture("/img/1.png", X<?php echo $h['number'];?>Texture);
    <?php endforeach; ?>
	


/* 	SterzhenTexture = gl.createTexture();
    setTexture("/img/sterzhen.png", SterzhenTexture); */
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

	
		<?php foreach($hands as $h): ?>
		initX<?php echo $h['number'];?>Buffers();       
		<?php endforeach; ?>
initLBuffers();
				//   
		
        setupTextures();
		
		setupLights();
		
        (function animloop(){
             
            setupWebGL();
            setMatrixUniforms();
			
			
			<?php foreach($hands as $h): ?>
            X<?php echo $h['number'];?>Draw();
            <?php endforeach; ?>
		LDraw();	
			           // 
			
			
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
				case 17:  
            angle3+=0.1;
            break;
        case 18: 
            angle3-=0.1;
            break;
        case 34: 
            zTranslation+=0.1;
            break;
        case 33: 
            zTranslation-=0.1;
            break;
        case 45: 
            xTranslation+=0.1;
            break;
        case 46: 
            xTranslation-=0.1;
            break;
        case 36: 
            yTranslation+=0.1;
            break;
        case 35: 
            yTranslation-=0.1;
            break;
		case 19: 
            xTranslation=0.0;
			yTranslation=0.0;
			zTranslation=-2.0;
			angle1=0.1;
			angle2=0.1;
			angle3=0.1;           
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


