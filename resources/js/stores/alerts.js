import { defineStore } from "pinia";
import axios from "axios";

export const useAlertsStore = defineStore("alerts", {
  state: () => ({
    /* Sample data (commonly used) */
    data: [],
  }),
  actions: {
    async fetch(params) {
      try {
        let options = {
          method: 'GET',
          headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
          },
          url: '/api/alerts',
          params: params,
        };
        await axios.request(options)
          .then(r => {
            this.data = r.data;
            console.log('r.alerts', r.data);
          });
      } catch (error) {
        console.log(error)
      }
    },
    async delete(id) {
      try {
        let options = {
          method: 'DELETE',
          headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
          },
          url: '/api/alerts',
          params: { id: id },
        };
        await axios.request(options)
          .then(r => {
            this.data = r.data;
            console.log('r.alerts', r.data);
          });
      } catch (error) {
        console.log(error)
      }
    },
  },



});
