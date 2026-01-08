<template>
  <div class="auth-container">
    <h2>Register</h2>
    <input v-model="name" type="text" placeholder="Name" />
    <input v-model="email" type="email" placeholder="Email" />
    <input v-model="password" type="password" placeholder="Password" />
    <input v-model="passwordConfirm" type="password" placeholder="Confirm Password" />
    <button @click="handleRegister" :disabled="loading">
      {{ loading ? 'Registering...' : 'Register' }}
    </button>
    <p v-if="message" class="message">{{ message }}</p>
    <p>
      Already have an account?
      <router-link to="/login">Login here</router-link>
    </p>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { apiRequest } from '@/services/api';

const router = useRouter();
const auth = useAuthStore();

const name = ref('');
const email = ref('');
const password = ref('');
const passwordConfirm = ref('');
const message = ref('');
const loading = ref(false);

async function handleRegister() {
  message.value = '';

  if (password.value !== passwordConfirm.value) {
    message.value = 'Passwords do not match';
    return;
  }

  loading.value = true;

  try {
    await apiRequest('/api/register', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        name: name.value,
        email: email.value,
        password: password.value,
        password_confirmation: passwordConfirm.value,
      }),
    });

    router.push('/login');
  } catch (error) {
    message.value = error.message || 'Registration failed';
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.auth-container {
  max-width: 400px;
  margin: 2rem auto;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
input {
  padding: 0.5rem;
  font-size: 1rem;
}
button {
  padding: 0.6rem;
  font-size: 1rem;
  cursor: pointer;
}
.message {
  color: red;
}
</style>