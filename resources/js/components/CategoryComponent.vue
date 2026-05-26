<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
      <h2>Категории товаров</h2>
      <button class="btn btn-success" type="button" @click="openCreateModal">
        <i class="fa-solid fa-plus"></i> Создать новую категорию
      </button>
    </div>

    <table class="table table-hover">
      <thead>
        <tr>
          <th>№</th>
          <th>Название</th>
          <th>Описание</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="cat in categories" :key="cat.id">
          <td>{{ cat.id }}</td>
          <td>{{ cat.name }}</td>
          <td>{{ cat.description }}</td>
          <td class="d-flex gap-2">
          <button class="btn btn-sm btn-primary" @click="openEditModal(cat)">Изменить</button>
          <button class="btn btn-sm btn-danger" @click="openDeleteModal(cat.id)">Удалить</button>
        </td>
        </tr>
        <tr v-if="categories.length === 0">
          <td colspan="4" class="text-center">Категории не найдены</td>
        </tr>
      </tbody>
    </table>

    <!-- Создаю -->
    <MDBModal v-model="createModal" tabindex="-1">
      <MDBModalHeader><MDBModalTitle>Новая категория</MDBModalTitle></MDBModalHeader>
      <MDBModalBody>
        <div class="mb-3">
          <label>Название</label>
          <input type="text" class="form-control" v-model="newCategory.name" placeholder="Введите название...">
        </div>
        <div class="mb-3">
          <label>Описание</label>
          <textarea class="form-control" v-model="newCategory.description" placeholder="Введите описание..."></textarea>
        </div>
      </MDBModalBody>
      <MDBModalFooter>
        <MDBBtn color="secondary" @click="createModal = false">Закрыть</MDBBtn>
        <MDBBtn color="primary" @click="saveNew">Сохранить</MDBBtn>
      </MDBModalFooter>
    </MDBModal>

    <!-- Редактирую -->
    <MDBModal v-model="editModal" tabindex="-1">
      <MDBModalHeader><MDBModalTitle>Изменить категорию</MDBModalTitle></MDBModalHeader>
      <MDBModalBody>
        <div class="mb-3">
          <label>Название</label>
          <input type="text" class="form-control" v-model="currentCategory.name">
        </div>
        <div class="mb-3">
          <label>Описание</label>
          <textarea class="form-control" v-model="currentCategory.description"></textarea>
        </div>
      </MDBModalBody>
      <MDBModalFooter>
        <MDBBtn color="secondary" @click="editModal = false">Закрыть</MDBBtn>
        <MDBBtn color="primary" @click="saveEdit">Обновить</MDBBtn>
      </MDBModalFooter>
    </MDBModal>

    <!-- Удаляю -->
    <MDBModal v-model="deleteModal" tabindex="-1">
      <MDBModalHeader><MDBModalTitle>Удаление</MDBModalTitle></MDBModalHeader>
      <MDBModalBody>Вы действительно хотите удалить эту категорию?</MDBModalBody>
      <MDBModalFooter>
        <MDBBtn color="secondary" @click="deleteModal = false">Отмена</MDBBtn>
        <MDBBtn color="danger" @click="confirmDelete">Удалить</MDBBtn>
      </MDBModalFooter>
    </MDBModal>
  </div>
</template>

<script>
import axios from "axios";
import { MDBModal, MDBModalHeader, MDBModalTitle, MDBModalBody, MDBModalFooter, MDBBtn } from "mdb-vue-ui-kit";

export default {
  components: { MDBModal, MDBModalHeader, MDBModalTitle, MDBModalBody, MDBModalFooter, MDBBtn },

  data() {
    return {
      categories: [],
      createModal: false,
      editModal: false,
      deleteModal: false,
      newCategory:     { name: '', description: '' },
      currentCategory: { id: null, name: '', description: '' },
      categoryToDelete: null,
    };
  },

  mounted() {
    this.loadCategories();
  },

  methods: {
    loadCategories() {
      axios.get("/api/categories").then(res => this.categories = res.data);
    },

    openCreateModal() {
      this.newCategory = { name: '', description: '' };
      this.createModal = true;
    },

    openEditModal(cat) {
      this.currentCategory = { ...cat };
      this.editModal = true;
    },

    openDeleteModal(id) {
      this.categoryToDelete = id;
      this.deleteModal = true;
    },

    saveNew() {
      axios.post("/api/categories", this.newCategory)
        .then(() => {
          this.loadCategories();
          this.createModal = false;
        })
        .catch(err => alert("Ошибка: " + (err.response?.data?.message ?? err.message)));
    },

    saveEdit() {
      axios.put(`/api/categories/${this.currentCategory.id}`, this.currentCategory)
        .then(() => {
          this.loadCategories();
          this.editModal = false;
        })
        .catch(err => alert("Ошибка: " + (err.response?.data?.message ?? err.message)));
    },

    confirmDelete() {
      axios.delete(`/api/categories/${this.categoryToDelete}`)
        .then(() => {
          this.loadCategories();
          this.deleteModal = false;
        })
        .catch(err => alert("Ошибка: " + (err.response?.data?.message ?? err.message)));
    },
  },
};
</script>