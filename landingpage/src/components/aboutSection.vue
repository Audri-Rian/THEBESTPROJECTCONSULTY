<template>
  <section class="about" id="sobre">
    <div class="about-container">
      <div class="about-content">
        <div class="section-header">
          <h2 class="section-title">Sobre Nós</h2>
          <div class="title-decoration"></div>
        </div>
        <div class="content-card">
          <h3 class="mission-title">Nossa Missão</h3>
          <p class="mission-text">
            Trazer beleza, sofisticação e autenticidade através de joias exclusivas feitas com amor e dedicação.
            Acreditamos que cada mulher merece peças únicas que reflitam sua personalidade.
          </p>
          <div class="divider"></div>
          <h3 class="history-title">Nossa História</h3>
          <p class="history-text">
            Fundada por apaixonados por moda e design, nossa marca nasceu com o propósito de valorizar a essência de cada
            pessoa. Combinamos tradição artesanal com design contemporâneo para criar peças atemporais.
          </p>
        </div>
      </div>
      <div class="about-image">
        <div class="image-container">
          <img
            src="https://images.unsplash.com/photo-1573408301185-9146fe634ad0?ixlib=rb-4.0.3&auto=format&fit=crop&w=2940&q=80"
            alt="Atelier de joias"
            class="hover-scale"
          />
          <div class="image-overlay"></div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'AboutSection',
  mounted() {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.content-card, .image-container').forEach(el => observer.observe(el));
  }
}
</script>

<style scoped>
.about {
  padding: 120px 2rem;
  background: linear-gradient(45deg, #0a0a0a 0%, #1a1a1a 100%);
  position: relative;
  overflow: hidden;
}

.about-container {
  max-width: 1400px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 5rem;
  align-items: center;
}

.section-header {
  margin-bottom: 4rem;
}

.section-title {
  font-family: 'Inter', sans-serif;
  font-size: 3.5rem;
  font-weight: 300;
  color: #ffffff;
  letter-spacing: 0.1em;
  position: relative;
  margin-bottom: 2rem;
}

.title-decoration {
  width: 80px;
  height: 2px;
  background: linear-gradient(90deg, #d4af37 0%, #a2854e 100%);
  margin-top: 1rem;
}

.content-card {
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(20px);
  border-radius: 24px;
  padding: 3rem;
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  transform: translateY(50px);
  opacity: 0;
  transition: all 0.8s cubic-bezier(0.23, 1, 0.32, 1);
}

.content-card.visible {
  transform: translateY(0);
  opacity: 1;
}

.mission-title, .history-title {
  font-family: 'Inter', sans-serif;
  font-size: 1.8rem;
  color: #d4af37;
  margin: 2rem 0 1.5rem;
  font-weight: 500;
  letter-spacing: 0.05em;
}

.mission-text, .history-text {
  font-family: 'Inter', sans-serif;
  font-size: 1.1rem;
  line-height: 1.8;
  color: rgba(255, 255, 255, 0.8);
  margin-bottom: 2rem;
  font-weight: 300;
}

.divider {
  height: 1px;
  background: linear-gradient(90deg, transparent 0%, rgba(212, 175, 55, 0.4) 50%, transparent 100%);
  margin: 2.5rem 0;
}

.image-container {
  position: relative;
  border-radius: 24px;
  overflow: hidden;
  transform: translateY(50px);
  opacity: 0;
  transition: all 0.8s cubic-bezier(0.23, 1, 0.32, 1) 0.2s;
}

.image-container.visible {
  transform: translateY(0);
  opacity: 1;
}

.image-container img {
  width: 100%;
  height: 600px;
  object-fit: cover;
  transition: transform 0.6s cubic-bezier(0.23, 1, 0.32, 1);
}

.image-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(45deg, rgba(16, 16, 16, 0.4) 0%, rgba(16, 16, 16, 0.1) 100%);
}

.hover-scale:hover img {
  transform: scale(1.03);
}

@media (max-width: 1024px) {
  .about-container {
    grid-template-columns: 1fr;
    gap: 3rem;
  }
  
  .section-title {
    font-size: 2.5rem;
  }
  
  .content-card {
    padding: 2rem;
  }
}
</style>