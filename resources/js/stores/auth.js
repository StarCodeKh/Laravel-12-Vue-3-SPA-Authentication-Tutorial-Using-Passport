import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
  }),

  getters: {
    isLoggedIn: (state) => !!state.user && !!state.token,
  },

  actions: {
    setUser(response) {
      this.user = response.user ?? null;
      this.token = response.token ?? null;

      if (this.token) {
        localStorage.setItem('token', this.token);
      } else {
        localStorage.removeItem('token');
      }
    },

    async fetchUser() {
      if (!this.token) {
        this.user = null;
        return;
      }

      try {
        const res = await fetch('/api/user', {
          headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${this.token}`,
          },
        });

        if (!res.ok) throw new Error();

        const data = await res.json();
        this.user = data.data;
      } catch {
        this.user = null;
        this.token = null;
        localStorage.removeItem('token');
      }
    },

    async logout() {
      if (!this.token) return;

      await fetch('/api/logout', {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Authorization': `Bearer ${this.token}`,
        },
      });

      this.user = null;
      this.token = null;
      localStorage.removeItem('token');
    },
  },
});