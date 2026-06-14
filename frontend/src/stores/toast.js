import { defineStore } from 'pinia';

export const useToastStore = defineStore('toast', {
  state: () => ({
    message: '',
    type: 'success', // 'success', 'error', 'info'
    show: false,
    timeout: null
  }),
  actions: {
    notify(msg, msgType = 'success', duration = 3000) {
      this.message = msg;
      this.type = msgType;
      this.show = true;
      
      if (this.timeout) {
        clearTimeout(this.timeout);
      }
      
      this.timeout = setTimeout(() => {
        this.show = false;
      }, duration);
    },
    close() {
      this.show = false;
      if (this.timeout) clearTimeout(this.timeout);
    }
  }
});
