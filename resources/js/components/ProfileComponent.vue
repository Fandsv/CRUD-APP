<template>
  <div class="container mt-4">
    <h3>Профиль</h3>

    <div v-if="loading" class="text-center mt-4">
      <div class="spinner-border" role="status"></div>
    </div>

    <div v-else class="card p-4">
      <h5 class="mb-3">Личные данные</h5>

      <div class="mb-3">
        <label>Имя</label>
        <input class="form-control" :value="profile.user?.name" disabled>
      </div>
      <div class="mb-3">
        <label>Email</label>
        <input class="form-control" :value="profile.user?.email" disabled>
      </div>
      <div class="mb-3">
        <label>Телефон</label>
        <input class="form-control" v-model="form.phone" placeholder="Введите телефон">
      </div>
      <div class="mb-3">
        <label>Адрес</label>
        <input class="form-control" v-model="form.address" placeholder="Введите адрес">
      </div>
      <div class="mb-3">
        <label>О себе</label>
        <textarea class="form-control" v-model="form.bio" placeholder="Расскажите о себе"></textarea>
      </div>

      <button class="btn btn-primary" @click="saveProfile">Сохранить</button>
      <div v-if="successMsg" class="alert alert-success mt-3">{{ successMsg }}</div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

const USER_ID = 1; 

export default {
  data() {
    return {
      loading: true,
      profile: {},
      form: { phone: '', address: '', bio: '' },
      successMsg: '',
    };
  },

  mounted() {
    this.loadProfile();
  },

  methods: {
    loadProfile() {
      axios.get(`/api/profiles/${USER_ID}`)
        .then(res => {
          this.profile = res.data;
          this.form.phone   = res.data.phone   ?? '';
          this.form.address = res.data.address ?? '';
          this.form.bio     = res.data.bio     ?? '';
        })
        .catch(() => {
          // профиля ещё нет - форму оставила пустой
          this.profile = {};
        })
        .finally(() => {
          this.loading = false;
        });
    },

    saveProfile() {
      const payload = { ...this.form, user_id: USER_ID };

      const request = this.profile.id
        ? axios.put(`/api/profiles/${USER_ID}`, payload)
        : axios.post('/api/profiles', payload);

      request
        .then(res => {
          this.profile = res.data;
          this.successMsg = 'Профиль сохранён!';
          setTimeout(() => this.successMsg = '', 3000);
        })
        .catch(err => alert("Ошибка: " + (err.response?.data?.message ?? err.message)));
    },
  },
};
</script>