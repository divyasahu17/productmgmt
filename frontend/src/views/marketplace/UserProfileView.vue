<template>
  <div class="user-profile-view">
    <div class="profile-container">
      <div class="profile-header">
        <h1>My Profile</h1>
        <p>Manage your account settings and security.</p>
      </div>

      <div class="profile-content">
        <!-- Personal Information Section -->
        <section class="profile-card">
          <h2>Personal Information</h2>
          <form @submit.prevent="handleProfileUpdate">
            <div class="form-group">
              <label>Full Name</label>
              <input type="text" v-model="profileForm.name" required />
              <span v-if="authStore.error && authStore.error.name" class="error">
                {{ authStore.error.name[0] }}
              </span>
            </div>
            <div class="form-group">
              <label>Email Address</label>
              <input type="email" v-model="profileForm.email" required />
              <span v-if="authStore.error && authStore.error.email" class="error">
                {{ authStore.error.email[0] }}
              </span>
            </div>
            
            <div v-if="profileSuccess" class="success-message">
              Profile updated successfully!
            </div>

            <button type="submit" class="submit-btn" :disabled="authStore.loading || requiresOtp">
              {{ authStore.loading ? 'Saving...' : 'Save Changes' }}
            </button>
          </form>
        </section>

        <!-- OTP Verification Section -->
        <section v-if="requiresOtp" class="profile-card otp-card">
          <h2>Verify Email Change</h2>
          <p class="otp-desc">We sent a 6-digit code to your <strong>current</strong> email address. Please enter it below to confirm the change.</p>
          <form @submit.prevent="handleOtpVerify">
            <div class="form-group">
              <label>OTP Code</label>
              <input type="text" v-model="otpForm.otp" required maxlength="6" pattern="\d{6}" placeholder="123456" class="otp-input" />
              <span v-if="authStore.error && authStore.error.otp" class="error">
                {{ authStore.error.otp[0] }}
              </span>
            </div>
            
            <div v-if="typeof authStore.error === 'string'" class="error-global">
              {{ authStore.error }}
            </div>

            <div class="otp-actions">
              <button type="button" class="cancel-btn" @click="cancelOtp">Cancel</button>
              <button type="submit" class="submit-btn" :disabled="authStore.loading">
                Verify
              </button>
            </div>
          </form>
        </section>

        <!-- Security Section -->
        <section class="profile-card">
          <h2>Security</h2>
          <form @submit.prevent="handlePasswordUpdate">
            <div class="form-group">
              <label>Current Password</label>
              <input type="password" v-model="passwordForm.current_password" required />
              <span v-if="authStore.error && authStore.error.current_password" class="error">
                {{ authStore.error.current_password[0] }}
              </span>
            </div>
            <div class="form-group">
              <label>New Password</label>
              <input type="password" v-model="passwordForm.password" required minlength="8" />
              <span v-if="authStore.error && authStore.error.password" class="error">
                {{ authStore.error.password[0] }}
              </span>
            </div>
            <div class="form-group">
              <label>Confirm New Password</label>
              <input type="password" v-model="passwordForm.password_confirmation" required minlength="8" />
            </div>

            <div v-if="passwordSuccess" class="success-message">
              Password updated successfully!
            </div>

            <div v-if="typeof authStore.error === 'string'" class="error-global">
              {{ authStore.error }}
            </div>

            <button type="submit" class="submit-btn" :disabled="authStore.loading">
              {{ authStore.loading ? 'Updating...' : 'Update Password' }}
            </button>
          </form>
        </section>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted, onUnmounted } from 'vue';
import { useAuthStore } from '../../stores/auth';

const authStore = useAuthStore();

const profileForm = reactive({
  name: '',
  email: ''
});

const passwordForm = reactive({
  current_password: '',
  password: '',
  password_confirmation: ''
});

const profileSuccess = ref(false);
const passwordSuccess = ref(false);
const requiresOtp = ref(false);

const otpForm = reactive({
  otp: ''
});

onMounted(() => {
  if (authStore.user) {
    profileForm.name = authStore.user.name;
    profileForm.email = authStore.user.email;
  }
});

onUnmounted(() => {
  authStore.error = null;
});

const handleProfileUpdate = async () => {
  profileSuccess.value = false;
  requiresOtp.value = false;
  const result = await authStore.updateProfile(profileForm);
  if (result) {
    if (result.requires_otp) {
      requiresOtp.value = true;
      otpForm.otp = '';
    } else {
      profileSuccess.value = true;
      setTimeout(() => { profileSuccess.value = false; }, 3000);
    }
  }
};

const handleOtpVerify = async () => {
  const success = await authStore.verifyEmailOtp(otpForm);
  if (success) {
    requiresOtp.value = false;
    profileSuccess.value = true;
    setTimeout(() => { profileSuccess.value = false; }, 3000);
  }
};

const cancelOtp = () => {
  requiresOtp.value = false;
  authStore.error = null;
  if (authStore.user) {
    profileForm.email = authStore.user.email; // Reset to original
  }
};

const handlePasswordUpdate = async () => {
  passwordSuccess.value = false;
  const success = await authStore.updatePassword(passwordForm);
  if (success) {
    passwordSuccess.value = true;
    passwordForm.current_password = '';
    passwordForm.password = '';
    passwordForm.password_confirmation = '';
    setTimeout(() => { passwordSuccess.value = false; }, 3000);
  }
};
</script>

<style scoped>
.user-profile-view {
  padding: 4rem 2rem;
  min-height: 100vh;
}

.profile-container {
  max-width: 800px;
  margin: 0 auto;
}

.profile-header {
  margin-bottom: 3rem;
  text-align: center;
}

.profile-header h1 {
  font-size: 2.5rem;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 0.5rem;
}

.profile-header p {
  color: #64748b;
  font-size: 1.125rem;
}

.profile-content {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.profile-card {
  background: white;
  padding: 2.5rem;
  border-radius: 16px;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
}

.profile-card h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  padding-bottom: 1rem;
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

.form-group input:focus {
  outline: none;
  border-color: #4f46e5;
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.submit-btn {
  width: auto;
  padding: 0.75rem 2rem;
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
}

.otp-card {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
}

.otp-desc {
  color: #64748b;
  font-size: 0.95rem;
  margin-bottom: 1.5rem;
}

.otp-input {
  font-size: 1.5rem !important;
  letter-spacing: 0.5rem;
  text-align: center;
  font-family: monospace;
}

.otp-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1.5rem;
}

.cancel-btn {
  padding: 0.75rem 1.5rem;
  background: white;
  color: #64748b;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.cancel-btn:hover {
  background: #f1f5f9;
  color: #0f172a;
}
</style>
