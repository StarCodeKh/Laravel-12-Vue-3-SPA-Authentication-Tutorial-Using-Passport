<template>
  <div class="dashboard-container">
    <h2>Welcome, {{ auth.user?.name || 'User' }}</h2>
    <p>Email: {{ auth.user?.email }}</p>
    <button @click="handleLogout" :disabled="loading">
      {{ loading ? 'Logging out...' : 'Logout' }}
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const auth = useAuthStore();

const loading = ref(false);

async function handleLogout() {
  loading.value = true;
  try {
    await auth.logout();
    router.push('/login');
  } catch (error) {
    alert('Logout failed.');
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.dashboard-container {
  max-width: 600px;
  margin: 2rem auto;
  text-align: center;
}
button {
  padding: 0.6rem 1.2rem;
  font-size: 1rem;
  cursor: pointer;
  margin-top: 1rem;
}
</style>