<template>
  <header class="header" :class="{ 'scrolled': isScrolled }">
    <nav class="nav">
      <div class="logo">MÔNICA ARAGÃO</div>
      <ul class="nav-links">
        <li v-for="(link, index) in links" :key="index">
          <a :href="link.url" class="nav-link hover-gold">{{ link.name }}</a>
        </li>
      </ul>
      
      <!-- Botões de Autenticação -->
      <div class="auth-buttons">
        <!-- Se o usuário estiver logado, mostra Dashboard -->
        <Link v-if="$page.props.auth.user" 
              :href="route('dashboard')"
              class="auth-btn auth-btn-dashboard">
          Dashboard
        </Link>
        
        <!-- Se não estiver logado, mostra Login e Register -->
        <template v-else>
          <Link :href="route('login')"
                class="auth-btn auth-btn-login">
            Entar
          </Link>
          <Link :href="route('register')"
                class="auth-btn auth-btn-register">
            Registrar
          </Link>
        </template>
      </div>
    </nav>
  </header>
</template>

<script lang="ts"> 
import { Link } from '@inertiajs/vue3'

export default {
  components: {
    Link
  },
  name: 'HeaderSection',
  data() {
    return {
      isScrolled: false,
      links: [
        { name: 'Inicio', url: '#home' },
        { name: 'Produtos', url: '#produtos' },
        { name: 'Lookbook', url: '#lookbook' },
        { name: 'Sobre', url: '#sobre' },
        { name: 'Contato', url: '#contato' }
      ]
    }
  },
  mounted() {
    window.addEventListener('scroll', this.handleScroll)
    this.animateElements()
  },
  methods: {
    handleScroll() {
      this.isScrolled = window.scrollY > 50
    },
    animateElements() {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible')
          }
        })
      }, { threshold: 0.1 })

      document.querySelectorAll('.nav-link, .auth-btn').forEach(el => observer.observe(el))
    }
  }
}
</script>

<style scoped>
.header {
  position: fixed;
  top: 0;
  width: 100%;
  background: rgba(16, 16, 16, 0.4);
  backdrop-filter: blur(20px);
  z-index: 1000;
  transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.header.scrolled {
  background: rgba(16, 16, 16, 0.9);
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
}

.nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 1400px;
  margin: 0 auto;
  padding: 1.5rem 2rem;
  gap: 2rem;
}

.logo {
  font-family: 'Playfair Display', serif;
  font-size: 2.2rem;
  font-weight: 500;
  background: linear-gradient(135deg, #d4af37 0%, #f9e795 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  letter-spacing: 0.1em;
  flex-shrink: 0;
}

.nav-links {
  display: flex;
  gap: 3rem;
  list-style: none;
  flex: 1;
  justify-content: center;
}

.nav-link {
  font-family: 'Inter', sans-serif;
  font-size: 1.1rem;
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  position: relative;
  padding: 0.5rem 0;
  transition: all 0.3s ease;
  opacity: 0;
  transform: translateY(10px);
}

.nav-link.visible {
  opacity: 1;
  transform: translateY(0);
}

.hover-gold::after {
  content: '';
  position: absolute;
  width: 0;
  height: 1px;
  bottom: 0;
  left: 0;
  background: #d4af37;
  transition: width 0.3s ease;
}

.hover-gold:hover {
  color: #d4af37;
}

.hover-gold:hover::after {
  width: 100%;
}

/* Estilos dos botões de autenticação */
.auth-buttons {
  display: flex;
  gap: 1rem;
  align-items: center;
  flex-shrink: 0;
}

.auth-btn {
  font-family: 'Inter', sans-serif;
  font-size: 0.95rem;
  padding: 0.6rem 1.5rem;
  border-radius: 6px;
  text-decoration: none;
  transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
  position: relative;
  opacity: 0;
  transform: translateY(10px);
  border: 1px solid transparent;
  backdrop-filter: blur(10px);
}

.auth-btn.visible {
  opacity: 1;
  transform: translateY(0);
}

/* Botão de Login - transparente com hover dourado */
.auth-btn-login {
  color: rgba(255, 255, 255, 0.85);
  background: transparent;
  border-color: rgba(255, 255, 255, 0.1);
}

.auth-btn-login:hover {
  color: #d4af37;
  border-color: rgba(212, 175, 55, 0.3);
  background: rgba(212, 175, 55, 0.05);
  transform: translateY(-2px);
}

/* Botão de Register - com gradiente dourado */
.auth-btn-register {
  background: linear-gradient(135deg, #d4af37 0%, #f9e795 100%);
  color: #1a1a1a;
  font-weight: 500;
  border-color: transparent;
}

.auth-btn-register:hover {
  background: linear-gradient(135deg, #f9e795 0%, #d4af37 100%);
  transform: translateY(-2px);
  box-shadow: 0 4px 20px rgba(212, 175, 55, 0.3);
}

/* Botão de Dashboard - estilo premium */
.auth-btn-dashboard {
  background: rgba(212, 175, 55, 0.1);
  color: #d4af37;
  border-color: rgba(212, 175, 55, 0.3);
  font-weight: 500;
}

.auth-btn-dashboard:hover {
  background: rgba(212, 175, 55, 0.2);
  border-color: rgba(212, 175, 55, 0.5);
  transform: translateY(-2px);
  box-shadow: 0 4px 20px rgba(212, 175, 55, 0.2);
}

@media (max-width: 1024px) {
  .nav {
    padding: 1rem 1.5rem;
    gap: 1.5rem;
  }
  
  .nav-links {
    gap: 2rem;
  }
  
  .nav-link {
    font-size: 1rem;
  }
  
  .logo {
    font-size: 2rem;
  }
  
  .auth-btn {
    font-size: 0.9rem;
    padding: 0.5rem 1.2rem;
  }
}

@media (max-width: 768px) {
  .nav-links {
    display: none;
  }
  
  .nav {
    justify-content: space-between;
  }
  
  .auth-buttons {
    gap: 0.8rem;
  }
  
  .auth-btn {
    font-size: 0.85rem;
    padding: 0.5rem 1rem;
  }
}

@media (max-width: 480px) {
  .nav {
    padding: 1rem;
  }
  
  .logo {
    font-size: 1.8rem;
  }
  
  .auth-buttons {
    gap: 0.5rem;
  }
  
  .auth-btn {
    font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
  }
}
</style>