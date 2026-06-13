<template>
  <div class="auth-container">
    <div class="auth-card">
      <h2>Login</h2>
      <form @submit.prevent="handleLogin">
        <div class="form-group">
          <label>Email</label>
          <input type="email" v-model="form.email" required />
          <span v-if="authStore.error && authStore.error.email" class="error">
            {{ authStore.error.email[0] }}
          </span>
        </div>
        <div class="form-group">
          <label>Password</label>
          <div class="input-wrapper">
            <input :type="showPassword ? 'text' : 'password'" v-model="form.password" required />
            <button type="button" class="eye-btn" @click="showPassword = !showPassword" tabindex="-1">
              <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            </button>
          </div>
          <span v-if="authStore.error && authStore.error.password" class="error">
            {{ authStore.error.password[0] }}
          </span>
        </div>
        
        <div v-if="typeof authStore.error === 'string'" class="error-global">
          {{ authStore.error }}
        </div>

        <button type="submit" class="submit-btn" :disabled="authStore.loading">
          {{ authStore.loading ? 'Signing in...' : 'Sign In' }}
        </button>
      </form>
      <div class="auth-links">
        <p>Don't have an account? <router-link to="/register">Register here</router-link></p>
        <p><router-link to="/forgot-password" class="forgot-link">Forgot Password?</router-link></p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, onUnmounted, ref } from 'vue';
import { useAuthStore } from '../../stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();
const showPassword = ref(false);

const form = reactive({
  email: '',
  password: ''
});

const handleLogin = async () => {
  const success = await authStore.login(form);
  if (success) {
    if (authStore.user?.role === 'admin') {
      authStore.error = 'Invalid credentials.';
      await authStore.logout();
      return;
    }
    router.push('/');
  }
};

onUnmounted(() => {
    authStore.error = null;
});
</script>

<style scoped>
.auth-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background-color: #f5f7fa;
}
.auth-card {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  width: 100%;
  max-width: 400px;
}
.form-group {
  margin-bottom: 1rem;
}
.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: bold;
}
.form-group input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
}
.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}
.input-wrapper input {
  width: 100%;
  padding: 0.5rem;
  padding-right: 2.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
}
.eye-btn {
  position: absolute;
  right: 0.5rem;
  background: transparent;
  border: none;
  color: #666;
  cursor: pointer;
  padding: 0;
  width: auto;
  margin-top: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}
.eye-btn svg {
  width: 20px;
  height: 20px;
}
button.submit-btn {
  width: 100%;
  padding: 0.75rem;
  background: linear-gradient(135deg, #4f46e5, #ec4899);
  color: white;
  border: none;
  border-radius: 9999px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 600;
  margin-top: 1rem;
  transition: all 0.3s ease;
  box-shadow: 0 4px 14px 0 rgba(79, 70, 229, 0.39);
}
button.submit-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
}
button:disabled {
  background: #cbd5e1;
  box-shadow: none;
}
.error {
  color: red;
  font-size: 0.875rem;
  margin-top: 0.25rem;
  display: block;
}
.error-global {
  color: white;
  background-color: #f44336;
  padding: 0.5rem;
  border-radius: 4px;
  margin-top: 1rem;
  font-size: 0.875rem;
}
.auth-links {
  margin-top: 1.5rem;
  text-align: center;
}
.auth-links p {
  margin: 0.5rem 0;
  color: #64748b;
}
.auth-links a {
  color: #4f46e5;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}
.auth-links a:hover {
  color: #4338ca;
  text-decoration: underline;
}
.forgot-link {
  font-size: 0.9rem;
}
</style>
