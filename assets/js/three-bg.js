/**
 * Universal Technology Corporation (UTC) - 3D Interactive Hero Canvas
 * Built with Three.js: Glowing holographic tech polyhedron with orbit rings & floating particles
 */

(function () {
    const canvasContainer = document.getElementById('three-hero-canvas');
    if (!canvasContainer || typeof THREE === 'undefined') return;

    let scene, camera, renderer;
    let mainMesh, wireframeMesh, particleSystem, ring1, ring2;
    let mouseX = 0, mouseY = 0;
    let targetX = 0, targetY = 0;
    let windowHalfX = window.innerWidth / 2;
    let windowHalfY = window.innerHeight / 2;

    function init() {
        const width = canvasContainer.clientWidth || window.innerWidth;
        const height = canvasContainer.clientHeight || 550;

        scene = new THREE.Scene();

        camera = new THREE.PerspectiveCamera(45, width / height, 1, 1000);
        camera.position.z = 240;

        renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.setSize(width, height);
        canvasContainer.appendChild(renderer.domElement);

        // Core 3D Geometry: Icosahedron Tech Crystal
        const geometry = new THREE.IcosahedronGeometry(45, 1);
        
        // Semi-transparent inner core
        const isDark = document.documentElement.classList.contains('dark');
        const coreColor = isDark ? 0x2563eb : 0x3b82f6;
        const wireColor = isDark ? 0x60a5fa : 0x1d4ed8;

        const material = new THREE.MeshPhongMaterial({
            color: coreColor,
            emissive: isDark ? 0x1e3a8a : 0x2563eb,
            emissiveIntensity: isDark ? 0.4 : 0.2,
            shininess: 80,
            wireframe: false,
            transparent: true,
            opacity: 0.85,
            flatShading: true
        });
        mainMesh = new THREE.Mesh(geometry, material);
        scene.add(mainMesh);

        // Tech Wireframe Outer Shell
        const wireframeGeometry = new THREE.IcosahedronGeometry(47, 1);
        const wireframeMaterial = new THREE.MeshBasicMaterial({
            color: wireColor,
            wireframe: true,
            transparent: true,
            opacity: 0.6
        });
        wireframeMesh = new THREE.Mesh(wireframeGeometry, wireframeMaterial);
        scene.add(wireframeMesh);

        // Orbital Ring 1
        const ringGeo1 = new THREE.TorusGeometry(65, 0.8, 16, 100);
        const ringMat1 = new THREE.MeshBasicMaterial({
            color: isDark ? 0x38bdf8 : 0x0284c7,
            transparent: true,
            opacity: 0.7
        });
        ring1 = new THREE.Mesh(ringGeo1, ringMat1);
        ring1.rotation.x = Math.PI / 3;
        scene.add(ring1);

        // Orbital Ring 2
        const ringGeo2 = new THREE.TorusGeometry(75, 0.6, 16, 100);
        const ringMat2 = new THREE.MeshBasicMaterial({
            color: isDark ? 0xf59e0b : 0xd97706,
            transparent: true,
            opacity: 0.55
        });
        ring2 = new THREE.Mesh(ringGeo2, ringMat2);
        ring2.rotation.y = Math.PI / 4;
        scene.add(ring2);

        // Floating Background Particles (Cyber Dust)
        const particleCount = 120;
        const particleGeometry = new THREE.BufferGeometry();
        const positions = new Float32Array(particleCount * 3);

        for (let i = 0; i < particleCount * 3; i += 3) {
            positions[i] = (Math.random() - 0.5) * 350;
            positions[i + 1] = (Math.random() - 0.5) * 300;
            positions[i + 2] = (Math.random() - 0.5) * 200;
        }

        particleGeometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        const particleMaterial = new THREE.PointsMaterial({
            color: isDark ? 0x38bdf8 : 0x2563eb,
            size: 3,
            transparent: true,
            opacity: 0.7
        });

        particleSystem = new THREE.Points(particleGeometry, particleMaterial);
        scene.add(particleSystem);

        // Lighting
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.8);
        scene.add(ambientLight);

        const pointLight = new THREE.PointLight(0x06b6d4, 2, 300);
        pointLight.position.set(50, 50, 100);
        scene.add(pointLight);

        const pointLight2 = new THREE.PointLight(0xf59e0b, 1.5, 300);
        pointLight2.position.set(-80, -50, 50);
        scene.add(pointLight2);

        // Event Listeners
        window.addEventListener('resize', onWindowResize, false);
        document.addEventListener('mousemove', onDocumentMouseMove, false);

        // Listen for Theme Switch
        window.addEventListener('utc-theme-changed', (e) => {
            const isDarkNow = e.detail.theme === 'dark';
            material.color.setHex(isDarkNow ? 0x2563eb : 0x3b82f6);
            material.emissive.setHex(isDarkNow ? 0x1e3a8a : 0x2563eb);
            wireframeMaterial.color.setHex(isDarkNow ? 0x60a5fa : 0x1d4ed8);
            ringMat1.color.setHex(isDarkNow ? 0x38bdf8 : 0x0284c7);
            particleMaterial.color.setHex(isDarkNow ? 0x38bdf8 : 0x2563eb);
        });

        animate();
    }

    function onWindowResize() {
        if (!canvasContainer) return;
        const width = canvasContainer.clientWidth;
        const height = canvasContainer.clientHeight || 550;
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
        renderer.setSize(width, height);
        windowHalfX = window.innerWidth / 2;
        windowHalfY = window.innerHeight / 2;
    }

    function onDocumentMouseMove(event) {
        mouseX = (event.clientX - windowHalfX) * 0.0005;
        mouseY = (event.clientY - windowHalfY) * 0.0005;
    }

    function animate() {
        requestAnimationFrame(animate);

        // Smooth rotation
        if (mainMesh) {
            mainMesh.rotation.x += 0.004;
            mainMesh.rotation.y += 0.006;
        }

        if (wireframeMesh) {
            wireframeMesh.rotation.x += 0.004;
            wireframeMesh.rotation.y += 0.006;
        }

        if (ring1) {
            ring1.rotation.z += 0.008;
            ring1.rotation.x += 0.003;
        }

        if (ring2) {
            ring2.rotation.z -= 0.007;
            ring2.rotation.y += 0.004;
        }

        if (particleSystem) {
            particleSystem.rotation.y += 0.001;
        }

        // Camera gentle parallax follow mouse
        targetX = mouseX * 25;
        targetY = -mouseY * 25;
        camera.position.x += (targetX - camera.position.x) * 0.05;
        camera.position.y += (targetY - camera.position.y) * 0.05;
        camera.lookAt(scene.position);

        renderer.render(scene, camera);
    }

    init();
})();
