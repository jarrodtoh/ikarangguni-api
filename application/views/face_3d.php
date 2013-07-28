<!DOCTYPE html>
<html lang="en">
  <head>
    <title>three.js webgl - loaders - OBJ MTL loader</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <style>
      body {
        font-family: Monospace;
        background-color: #000;
        color: #fff;
        margin: 0px;
        overflow: hidden;
      }
      #info {
        color: #fff;
        position: absolute;
        top: 10px;
        width: 100%;
        text-align: center;
        z-index: 100;
        display:block;
      }
      #info a, .button { color: #f00; font-weight: bold; text-decoration: underline; cursor: pointer }
    </style>
  </head>

  <body>
    <div id="info">
      <a href="http://threejs.org" target="_blank">three.js</a> - OBJMTLLoader test
    </div>

    <script src="/assets/js/three.min.js"></script>
    <script src="/assets/js/MTLLoader.js"></script>
    <script src="/assets/js/OBJMTLLoader.js"></script>
    <script src="/assets/js/Detector.js"></script>
    <script src="/assets/js/stats.min.js"></script>

    <script>

      var container, stats;

      var camera, scene, renderer;

      var mouseX = 0, mouseY = 0;

      var windowHalfX = window.innerWidth / 2;
      var windowHalfY = window.innerHeight / 2;


      init();
      animate();


      function init() {

        container = document.createElement('div');
        document.body.appendChild(container);

        camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 1, 2000);
        camera.position.z = 350;

        // scene

        scene = new THREE.Scene();

        var ambient = new THREE.AmbientLight(0xEFEFEF);
        scene.add(ambient);

        var directionalLight = new THREE.DirectionalLight(0xffeedd);
        directionalLight.position.set(0, 0, 1).normalize();
        scene.add(directionalLight);

        // model

        var loader = new THREE.OBJMTLLoader();
        loader.addEventListener('load', function(event) {
          var object = event.content;
          object.position.y = -40;
          object.position.z = -40;
          scene.add(object);
        });
        loader.load('<?php echo $OBJFACE_FILES['OBJ']; ?>', '<?php echo $OBJFACE_FILES['MTL']; ?>');

        //

        renderer = new THREE.WebGLRenderer();
        renderer.setSize(window.innerWidth, window.innerHeight);
        container.appendChild(renderer.domElement);

        document.addEventListener('mousemove', onDocumentMouseMove, false);

        //

        window.addEventListener('resize', onWindowResize, false);

      }

      function onWindowResize() {

        windowHalfX = window.innerWidth / 2;
        windowHalfY = window.innerHeight / 2;

        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();

        renderer.setSize(window.innerWidth, window.innerHeight);

      }

      function onDocumentMouseMove(event) {

        mouseX = (event.clientX - windowHalfX) / 2;
        mouseY = (event.clientY - windowHalfY) / 2;

      }

      //

      function animate() {

        requestAnimationFrame(animate);
        render();

      }

      function render() {

        camera.position.x += (mouseX - camera.position.x) * .05;
        camera.position.y += (-mouseY - camera.position.y) * .05;

        camera.lookAt(scene.position);

        renderer.render(scene, camera);

      }

    </script>

  </body>
</html>
