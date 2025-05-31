<template>
  <section class="luxury" id="luxury">
    <div class="luxury-container">
      <div class="luxury-content">
        <div class="section-header">
          <h2 class="section-title">Brilho, Luxo<br>& Exclusividade</h2>
          <div class="title-decoration"></div>
        </div>
        <p class="luxury-text">
          Cada peça é desenhada para destacar a beleza única de quem a usa. Use nossas joias como
          uma forma de expressão, poder e sofisticação. Transforme momentos especiais em memórias
          inesquecíveis.
        </p>
      </div>
      <div class="luxury-grid">
        <div class="image-card" v-for="(image, index) in images" :key="index">
          <div class="image-container">
            <img
              :src="image.src"
              :alt="image.alt"
              class="hover-scale"
            />
            <div class="image-overlay"></div>
            <div class="content-overlay">
              <h3 class="image-title">{{ image.title }}</h3>
              <p class="image-description">{{ image.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="ambient-light"></div>
  </section>
</template>

<script>
export default {
  name: 'LuxurySection',
  data() {
    return {
      images: [
        {
          src: 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?ixlib=rb-4.0.3&auto=format&fit=crop&w=2940&q=80',
          alt: 'Joia elegante 1',
          title: 'Coleção Aurora',
          description: 'Brilho que transcende o tempo'
        },
        {
          src: 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?ixlib=rb-4.0.3&auto=format&fit=crop&w=2940&q=80',
          alt: 'Joia elegante 2',
          title: 'Série Éden',
          description: 'Luxo em sua forma mais pura'
        }
      ]
    }
  },
  mounted() {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.image-card, .luxury-content').forEach(el => observer.observe(el));
  }
}
</script>

<style scoped>
.luxury {
  padding: 120px 2rem;
  background: #0a0a0a;
  position: relative;
  overflow: hidden;
}

.luxury-container {
  max-width: 1400px;
  margin: 0 auto;
  position: relative;
  z-index: 2;
}

.section-header {
  margin-bottom: 4rem;
}

.section-title {
  font-family: 'Playfair Display', serif;
  font-size: 4rem;
  color: #d4af37;
  line-height: 1.1;
  letter-spacing: 0.05em;
  margin-bottom: 2rem;
}

.title-decoration {
  width: 120px;
  height: 2px;
  background: linear-gradient(90deg, #d4af37 0%, transparent 100%);
  margin-top: 1.5rem;
}

.luxury-text {
  font-family: 'Inter', sans-serif;
  font-size: 1.2rem;
  color: rgba(255, 255, 255, 0.85);
  line-height: 1.8;
  max-width: 600px;
  margin-bottom: 4rem;
}

.luxury-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 2rem;
}

.image-card {
  position: relative;
  border-radius: 24px;
  overflow: hidden;
  transform: translateY(50px);
  opacity: 0;
  transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
}

.image-card.visible {
  transform: translateY(0);
  opacity: 1;
}

.image-container {
  position: relative;
  height: 600px;
}

.image-container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s cubic-bezier(0.23, 1, 0.32, 1);
}

.image-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(180deg, rgba(16, 16, 16, 0.4) 0%, rgba(16, 16, 16, 0.1) 100%);
}

.content-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  padding: 2rem;
  background: linear-gradient(180deg, transparent 0%, rgba(16, 16, 16, 0.9) 100%);
}

.image-title {
  font-family: 'Playfair Display', serif;
  font-size: 2rem;
  color: #d4af37;
  margin-bottom: 0.5rem;
}

.image-description {
  font-family: 'Inter', sans-serif;
  color: rgba(255, 255, 255, 0.85);
  font-size: 1.1rem;
}

.hover-scale:hover img {
  transform: scale(1.05);
}

.ambient-light {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 100%;
  height: 100%;
  background: radial-gradient(circle at 50% 50%, rgba(212, 175, 55, 0.05) 0%, transparent 60%);
  transform: translate(-50%, -50%);
  z-index: 1;
}

@media (max-width: 1024px) {
  .luxury-grid {
    grid-template-columns: 1fr;
  }
  
  .section-title {
    font-size: 3rem;
  }
  
  .image-container {
    height: 500px;
  }
}

@media (max-width: 768px) {
  .luxury {
    padding: 80px 1.5rem;
  }
  
  .section-title {
    font-size: 2.5rem;
  }
  
  .luxury-text {
    font-size: 1.1rem;
  }
}
</style>