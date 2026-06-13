<template>
  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-header">
        <h1>Forgot Password</h1>
        <p v-if="!otpSent">Enter your email to receive a reset code.</p>
        <p v-else>We've sent a 6-digit code to <strong>{{ email }}</strong>.</p>
      </div>

      <!-- Step 1: Request OTP -->
      <form v-if="!otpSent" @submit.prevent="handleRequestOtp" class="auth-form">
        <div class="form-group">
          <label>Email Address</label>
          <input type="email" v-model="email" required placeholder="you@example.com" />
          <span v-if="authStore.error && authStore.error.email" class="error">
            {{ authStore.error.email[0] }}
          </span>
        </div>

        <div v-if="typeof authStore.error === 'string'" class="error-global">
          {{ authStore.error }}
        </div>

        <button type="submit" class="submit-btn" :disabled="authStore.loading">
          {{ authStore.loading ? 'Sending...' : 'Send Reset Code' }}
        </button>

        <div class="auth-links">
          <router-link to="/login">Back to Login</router-link>
        </div>
      </form>

      <!-- Step 2: Verify OTP and Reset Password -->
      <form v-else @submit.prevent="handleResetPassword" class="auth-form">
        <div class="form-group">
          <label>Reset Code (OTP)</label>
          <input type="text" v-model="resetForm.otp" required maxlength="6" pattern="\d{6}" placeholder="123456" class="otp-input" />
          <span v-if="authStore.error && authStore.error.otp" class="error">
            {{ authStore.error.otp[0] }}
          </span>
        </div>

        <div class="form-group">
          <label>New Password</label>
          <div class="input-wrapper">
            <input :type="showPassword ? 'text' : 'password'" v-model="resetForm.password" required minlength="8" />
            <button type="button" class="eye-btn" @click="showPassword = !showPassword" tabindex="-1">
              <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            </button>
          </div>
          <span v-if="authStore.error && authStore.error.password" class="error">
            {{ authStore.error.password[0] }}
          </span>
        </div>

        <div class="form-group">
          <label>Confirm New Password</label>
          <div class="input-wrapper">
            <input :type="showConfirmPassword ? 'text' : 'password'" v-model="resetForm.password_confirmation" required minlength="8" />
            <button type="button" class="eye-btn" @click="showConfirmPassword = !showConfirmPassword" tabindex="-1">
              <svg v-if="!showConfirmPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            </button>
          </div>
        </div>

        <div v-if="typeof authStore.error === 'string'" class="error-global">
          {{ authStore.error }}
        </div>

        <div v-if="successMessage" class="success-message">
          {{ successMessage }}
        </div>

        <button type="submit" class="submit-btn" :disabled="authStore.loading || successMessage !== ''">
          {{ authStore.loading ? 'Resetting...' : 'Reset Password' }}
        </button>

        <div class="auth-links">
          <a href="#" @click.prevent="otpSent = false">Did not receive code?</a>
          <router-link to="/login">Back to Login</router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const email = ref('');
const otpSent = ref(false);
const successMessage = ref('');
const showPassword = ref(false);
const showConfirmPassword = ref(false);

const resetForm = reactive({
  email: '',
  otp: '',
  password: '',
  password_confirmation: ''
});

onUnmounted(() => {
  authStore.error = null;
});

const handleRequestOtp = async () => {
  const success = await authStore.requestPasswordReset(email.value);
  if (success) {
    otpSent.value = true;
    resetForm.email = email.value;
  }
};

const handleResetPassword = async () => {
  const success = await authStore.resetPassword(resetForm);
  if (success) {
    successMessage.value = 'Password reset successfully! Redirecting to login...';
    setTimeout(() => {
      router.push('/login');
    }, 2000);
  }
};
</script>

<style scoped>
.auth-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: calc(100vh - 80px);
  padding: 2rem;
  background: #f8fafc;
}

.auth-card {
  background: white;
  padding: 3rem;
  border-radius: 16px;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
  width: 100%;
  max-width: 480px;
}

.auth-header {
  text-align: center;
  margin-bottom: 2rem;
}

.auth-header h1 {
  font-size: 2rem;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 0.5rem;
}

.auth-header p {
  color: #64748b;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #334155;
  font-size: 0.95rem;
}

.form-group input {
  width: 100%;
  padding: 0.875rem;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  transition: all 0.2s;
  font-size: 1rem;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-wrapper input {
  width: 100%;
  padding: 0.875rem;
  padding-right: 2.5rem;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  transition: all 0.2s;
  font-size: 1rem;
}

.input-wrapper input:focus {
  outline: none;
  border-color: #4f46e5;
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.eye-btn {
  position: absolute;
  right: 0.75rem;
  background: transparent;
  border: none;
  color: #64748b;
  cursor: pointer;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.eye-btn:hover {
  color: #0f172a;
}

.eye-btn svg {
  width: 20px;
  height: 20px;
}

.form-group input:focus {
  outline: none;
  border-color: #4f46e5;
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.otp-input {
  font-size: 1.5rem !important;
  letter-spacing: 0.5rem;
  text-align: center;
  font-family: monospace;
}

.submit-btn {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, #4f46e5, #ec4899);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 1rem;
}

.submit-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

.submit-btn:disabled {
  background: #cbd5e1;
  cursor: not-allowed;
}

.error {
  color: #ef4444;
  font-size: 0.875rem;
  margin-top: 0.5rem;
  display: block;
}

.error-global {
  background: #fef2f2;
  color: #ef4444;
  padding: 1rem;
  border-radius: 8px;
  margin: 1rem 0;
  font-size: 0.9rem;
}

.success-message {
  background: #f0fdf4;
  color: #16a34a;
  padding: 1rem;
  border-radius: 8px;
  margin: 1rem 0;
  font-weight: 500;
  text-align: center;
}

.auth-links {
  margin-top: 1.5rem;
  text-align: center;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
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
</style>
