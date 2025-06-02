<template>
  <section class="products" id="produtos">
    <div class="section-header">
      <h2 class="section-title">Nossa Coleção</h2>
      <div class="title-decoration"></div>
    </div>
    
    <div class="products-grid">
      <div 
        class="product-card" 
        v-for="product in products" 
        :key="product.id"
        @mouseenter="showOverlay(product.id)"
        @mouseleave="hideOverlay(product.id)"
      >
        <div class="image-container">
          <img :src="product.image" :alt="product.name" class="product-image" />
          <div class="image-overlay"></div>
          <div class="hover-overlay" :class="{ 'active': activeOverlay === product.id }">
            <button class="quick-view" @click="openQuickView(product)">
              <i class="fas fa-eye"></i> Visualização Rápida
            </button>
          </div>
        </div>
        <div class="product-info">
          <h3 class="product-name">{{ product.name }}</h3>
          <div class="price-container">
            <p class="product-price">{{ product.price }}</p>
            <div class="accent-line"></div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'ProductsSection',
  data() {
    return {
      activeOverlay: null,
      products: [
        {
          id: 1,
          name: 'Anel de Diamante',
          price: 'R$ 2.999,00',
          image: 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?ixlib=rb-4.0.3&auto=format&fit=crop&w=2940&q=80'
        },
        {
          id: 2,
          name: 'Colar de Pérolas',
          price: 'R$ 1.899,00',
          image: 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?ixlib=rb-4.0.3&auto=format&fit=crop&w=2940&q=80'
        },
        {
          id: 3,
          name: 'Brincos de Ouro',
          price: 'R$ 999,00',
          image: 'https://images.unsplash.com/photo-1535632787350-4e68ef0ac584?ixlib=rb-4.0.3&auto=format&fit=crop&w=2940&q=80'
        },
        {
          id: 4,
          name: 'Pulseira de Prata',
          price: 'R$ 599,00',
          image: 'https://images.unsplash.com/photo-1611591437281-460bfbe1220a?ixlib=rb-4.0.3&auto=format&fit=crop&w=2940&q=80'
        }
      ]
    };
  },
  methods: {
    showOverlay(id) {
      this.activeOverlay = id;
    },
    hideOverlay(id) {
      this.activeOverlay = null;
    },
    openQuickView(product) {
      // Implement quick view functionality
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

    document.querySelectorAll('.product-card').forEach(el => observer.observe(el));
  }
};
</script>

<style scoped>
.products {
  padding: 120px 2rem;
  background: #0a0a0a;
  position: relative;
}

.section-header {
  text-align: center;
  margin-bottom: 4rem;
}

.section-title {
  font-family: 'Playfair Display', serif;
  font-size: 4rem;
  color: #d4af37;
  letter-spacing: 0.1em;
  margin-bottom: 1.5rem;
}

.title-decoration {
  width: 100px;
  height: 2px;
  background: linear-gradient(90deg, #d4af37 0%, transparent 100%);
  margin: 0 auto;
}

.products-grid {
  max-width: 1400px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.product-card {
  background: rgba(255, 255, 255, 0.03);
  border-radius: 24px;
  overflow: hidden;
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.05);
  transform: translateY(50px);
  opacity: 0;
  transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
}

.product-card.visible {
  transform: translateY(0);
  opacity: 1;
}

.image-container {
  position: relative;
  height: 400px;
  overflow: hidden;
}

.product-image {
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
  background: linear-gradient(180deg, rgba(16, 16, 16, 0.4) 0%, transparent 100%);
}

.hover-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(16, 16, 16, 0.6);
  opacity: 0;
  transition: opacity 0.4s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.hover-overlay.active {
  opacity: 1;
}

.quick-view {
  background: rgba(212, 175, 55, 0.2);
  color: #d4af37;
  padding: 1rem 2rem;
  border: 1px solid rgba(212, 175, 55, 0.3);
  border-radius: 50px;
  font-family: 'Inter', sans-serif;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
}

.quick-view:hover {
  background: rgba(212, 175, 55, 0.3);
  transform: translateY(-2px);
}

.product-info {
  padding: 1.5rem;
  text-align: center;
}

.product-name {
  font-family: 'Inter', sans-serif;
  font-size: 1.3rem;
  color: rgba(255, 255, 255, 0.9);
  margin-bottom: 1rem;
  font-weight: 500;
}

.price-container {
  position: relative;
}

.product-price {
  font-family: 'Inter', sans-serif;
  font-size: 1.4rem;
  color: #d4af37;
  font-weight: 600;
  background: linear-gradient(135deg, #d4af37 0%, #f9e795 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.accent-line {
  width: 60px;
  height: 1px;
  background: linear-gradient(90deg, #d4af37 0%, transparent 100%);
  margin: 0.5rem auto;
  transition: width 0.3s ease;
}

.product-card:hover .accent-line {
  width: 100px;
}

@media (max-width: 1024px) {
  .section-title {
    font-size: 3rem;
  }
  
  .products-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .products {
    padding: 80px 1.5rem;
  }
  
  .section-title {
    font-size: 2.5rem;
  }
  
  .products-grid {
    grid-template-columns: 1fr;
  }
  
  .image-container {
    height: 300px;
  }
}
</style>