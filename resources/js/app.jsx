import "./bootstrap";

const canvas = document.createElement("canvas");
canvas.id = "stars";
document.body.appendChild(canvas);

const ctx = canvas.getContext("2d");

let stars = [];
let mouse = { x: null, y: null };

const STAR_LAYERS = [
  { count: 80, speed: 0.2, size: 1 },
  { count: 50, speed: 0.4, size: 1.5 },
  { count: 30, speed: 0.7, size: 2 }
];

function resize() {
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;
}
resize();
window.addEventListener("resize", resize);

window.addEventListener("mousemove", (e) => {
  mouse.x = e.clientX;
  mouse.y = e.clientY;
});

function createStars() {
  stars = [];

  STAR_LAYERS.forEach(layer => {
    for (let i = 0; i < layer.count; i++) {
      stars.push({
        x: Math.random() * canvas.width,
        y: Math.random() * canvas.height,
        radius: Math.random() * layer.size,
        speed: layer.speed,
        opacity: Math.random(),
        color: Math.random() > 0.8
          ? `rgba(150,150,255,${Math.random()})`
          : `rgba(255,255,255,${Math.random()})`
      });
    }
  });
}

function drawStars() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);

  stars.forEach(star => {
    ctx.beginPath();

    ctx.shadowBlur = 8;
    ctx.shadowColor = star.color;

    ctx.arc(star.x, star.y, star.radius, 0, Math.PI * 2);
    ctx.fillStyle = star.color;
    ctx.fill();

    star.y += star.speed;

    if (mouse.x && mouse.y) {
      star.x += (mouse.x - canvas.width / 2) * 0.00005;
      star.y += (mouse.y - canvas.height / 2) * 0.00005;
    }

    if (star.y > canvas.height) {
      star.y = 0;
      star.x = Math.random() * canvas.width;
    }
  });

  requestAnimationFrame(drawStars);
}

createStars();
drawStars();

import { createInertiaApp } from '@inertiajs/react'
import { createRoot } from 'react-dom/client'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

createInertiaApp({
    resolve: name => resolvePageComponent(
        `./Pages/${name}.jsx`,
        import.meta.glob('./Pages/**/*.jsx')
    ),
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />)
    },
})