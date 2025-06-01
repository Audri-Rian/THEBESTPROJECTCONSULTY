<template>
  <footer class="footer" id="contato">
    <div class="footer-container">
      <div class="footer-content">
        <div class="footer-brand">
          <h3 class="logo">Monica Aragão</h3>
          <div class="logo-decoration"></div>
        </div>

        <div class="footer-grid">
          <div class="footer-column">
            <h4 class="column-title">Explore</h4>
            <ul class="footer-links">
              <li><a href="#produtos" class="hover-gold">Produtos</a></li>
              <li><a href="#lookbook" class="hover-gold">Lookbook</a></li>
              <li><a href="#sobre" class="hover-gold">Sobre nós</a></li>
            </ul>
          </div>

          <div class="footer-column">
            <h4 class="column-title">Contato</h4>
            <div class="contact-info">
              <p>R. Alcídes Cursinho - Praça da Bandeira - Arcoverde</p>
              <p>(87) 99991-0094</p>
            </div>
          </div>

           <div class="footer-column">
            <h4 class="column-title">Conecte-se</h4>
            <div class="footer-social">
              <!-- Instagram -->
              <a href="https://www.instagram.com/monica.aragao.1422/" 
                 class="social-icon instagram" 
                 aria-label="Instagram"
                 target="_blank"
                 rel="noopener noreferrer">
                <i class="fab fa-instagram"></i>
              </a>
              
              <!-- Facebook -->
              <a href="https://www.facebook.com/profile.php?id=100006700590318&ref=_ig_profile_ac" 
                 class="social-icon facebook" 
                 aria-label="Facebook"
                 target="_blank"
                 rel="noopener noreferrer">
                <i class="fab fa-facebook-f"></i>
              </a>
              
              <!-- WhatsApp -->
              <a href="https://wa.me/5587999910094" 
                 class="social-icon whatsapp" 
                 aria-label="WhatsApp"
                 target="_blank"
                 rel="noopener noreferrer">
                <i class="fab fa-whatsapp"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="footer-copyright">
        <p>&copy; 2025 Monica Aragão - Todos os direitos reservados.</p>
      </div>
    </div>
  </footer>
</template>

<script lang="ts">
export default {
  name: 'FooterSection',
  mounted() {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.footer-column, .footer-brand').forEach(el => observer.observe(el));
  }
}
</script>

<style scoped>
.footer {
  background: linear-gradient(180deg, #0a0a0a 0%, #1a1a1a 100%);
  padding: 80px 2rem 40px;
  border-top: 1px solid rgba(212, 175, 55, 0.1);
  position: relative;
}

.footer-container {
  max-width: 1400px;
  margin: 0 auto;
}

.footer-content {
  backdrop-filter: blur(10px);
  background: rgba(255, 255, 255, 0.03);
  border-radius: 24px;
  padding: 4rem;
  margin-bottom: 3rem;
  border: 1px solid rgba(255, 255, 255, 0.05);
}

.footer-brand {
  text-align: center;
  margin-bottom: 4rem;
  opacity: 0;
  transform: translateY(30px);
  transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
}

.footer-brand.visible {
  opacity: 1;
  transform: translateY(0);
}

.logo {
  font-family: 'Playfair Display', serif;
  font-size: 2.5rem;
  color: #d4af37;
  letter-spacing: 0.1em;
  margin-bottom: 1rem;
}

.logo-decoration {
  width: 120px;
  height: 2px;
  background: linear-gradient(90deg, #d4af37 0%, transparent 100%);
  margin: 0 auto;
}

.footer-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 4rem;
}

.column-title {
  font-family: 'Inter', sans-serif;
  font-size: 1.2rem;
  color: #d4af37;
  margin-bottom: 2rem;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

.footer-links {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  list-style: none;
}

.hover-gold {
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  font-size: 1.1rem;
  position: relative;
  transition: all 0.3s ease;
}

.hover-gold:hover {
  color: #d4af37;
  transform: translateX(5px);
}

.hover-gold::before {
  content: '';
  position: absolute;
  width: 0;
  height: 1px;
  bottom: -2px;
  left: 0;
  background: #d4af37;
  transition: width 0.3s ease;
}

.hover-gold:hover::before {
  width: 100%;
}

.contact-info p {
  color: rgba(255, 255, 255, 0.8);
  margin-bottom: 1rem;
  line-height: 1.6;
}

.footer-social {
  display: flex;
  gap: 1.5rem;
}

.social-icon {
  width: 45px;
  height: 45px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(212, 175, 55, 0.1);
  border-radius: 12px;
  color: #d4af37;
  font-size: 1.2rem;
  transition: all 0.3s ease;
  border: 1px solid rgba(212, 175, 55, 0.2);
}

.social-icon:hover {
  background: rgba(212, 175, 55, 0.2);
  transform: translateY(-3px);
  box-shadow: 0 4px 15px rgba(212, 175, 55, 0.1);
}

.footer-copyright {
  text-align: center;
  padding-top: 2rem;
  border-top: 1px solid rgba(255, 255, 255, 0.05);
  color: rgba(255, 255, 255, 0.6);
  font-size: 0.9rem;
}

.footer-column {
  opacity: 0;
  transform: translateY(30px);
  transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
}

.footer-column.visible {
  opacity: 1;
  transform: translateY(0);
}

@media (max-width: 1024px) {
  .footer-grid {
    grid-template-columns: 1fr;
    gap: 3rem;
  }
  
  .footer-content {
    padding: 2rem;
  }
  
  .footer-brand {
    margin-bottom: 3rem;
  }
}
</style>