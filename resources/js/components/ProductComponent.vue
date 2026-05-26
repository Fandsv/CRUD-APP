<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
      <h2>Мои товары</h2>
      <button class="btn btn-success" @click="openCreateModal">
        <i class="fa-solid fa-plus"></i> Создать новый товар
      </button>
    </div>

    <table class="table">
  <thead>
    <tr>
      <th>№</th><th>Название</th><th>Цена</th><th>Категория</th><th>Действия</th>
    </tr>
  </thead>
  <tbody>
    <tr v-for="(product, index) in products" :key="product.id">
  <td>{{ index + 1 }}</td>
  <td>{{ product.name }}</td>
  <td>{{ product.price }} сом</td>
  <td>{{ product.category ? product.category.name : 'Без категории' }}</td>
  <td class="d-flex gap-2">
    <button class="btn btn-sm btn-primary" @click="openEditModal(product)">Изменить</button>
    <button class="btn btn-sm btn-danger" @click="openDeleteModal(product.id)">Удалить</button>
  </td>
</tr>
    <tr v-if="products.length === 0">
      <td colspan="5" class="text-center">Товары не найдены</td>
    </tr>
  </tbody>
</table>

    <MDBModal v-model="createModal" tabindex="-1">
      <MDBModalHeader><MDBModalTitle>Создать товар</MDBModalTitle></MDBModalHeader>
      <MDBModalBody>
        <input class="form-control mb-2" v-model="newProduct.name" placeholder="Название">
        <input type="number" class="form-control mb-2" v-model.number="newProduct.price" placeholder="Цена">
        <select class="form-control" v-model="newProduct.category_id">
          <option value="" disabled>Выберите категорию</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
        </select>
      </MDBModalBody>
      <MDBModalFooter>
        <button class="btn btn-secondary" @click="createModal = false">Закрыть</button>
        <button class="btn btn-primary" @click="saveProduct">Сохранить</button>
      </MDBModalFooter>
    </MDBModal>

    <MDBModal v-model="editModal" tabindex="-1">
      <MDBModalHeader><MDBModalTitle>Редактировать товар</MDBModalTitle></MDBModalHeader>
      <MDBModalBody>
        <input class="form-control mb-2" v-model="editProductData.name">
        <input type="number" class="form-control mb-2" v-model.number="editProductData.price">
        <select class="form-control" v-model="editProductData.category_id">
          <option value="" disabled>Выберите категорию</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
        </select>
      </MDBModalBody>
      <MDBModalFooter>
        <button class="btn btn-secondary" @click="editModal = false">Закрыть</button>
        <button class="btn btn-primary" @click="updateProduct">Обновить</button>
      </MDBModalFooter>
    </MDBModal>

    <MDBModal v-model="deleteModal" tabindex="-1">
      <MDBModalHeader><MDBModalTitle>Удаление</MDBModalTitle></MDBModalHeader>
      <MDBModalBody>Вы действительно хотите удалить этот товар?</MDBModalBody>
      <MDBModalFooter>
        <button class="btn btn-secondary" @click="deleteModal = false">Отмена</button>
        <button class="btn btn-danger" @click="confirmDelete">Удалить</button>
      </MDBModalFooter>
    </MDBModal>
  </div>
</template>

<script>
import axios from "axios";
import { MDBModal, MDBModalHeader, MDBModalTitle, MDBModalBody, MDBModalFooter } from "mdb-vue-ui-kit";

export default {
  components: { MDBModal, MDBModalHeader, MDBModalTitle, MDBModalBody, MDBModalFooter },

  data() {
    return {
      products: [],
      categories: [],
      createModal: false,
      editModal: false,
      deleteModal: false,
      newProduct:      { name: '', price: '', category_id: '' },
      editProductData: { id: null, name: '', price: '', category_id: '' },
      productToDelete: null,
    };
  },

  mounted() {
    this.loadProducts();
    this.loadCategories();
  },

  methods: {
    loadProducts() {
      axios.get("/api/products").then(res => this.products = res.data);
    },
    loadCategories() {
      axios.get("/api/categories").then(res => this.categories = res.data);
    },

    openCreateModal() {
      this.newProduct = { name: '', price: '', category_id: '' };
      this.createModal = true;
    },

    openEditModal(product) {
      this.editProductData = { ...product, category_id: product.category?.id ?? product.category_id };
      this.editModal = true;
    },

    openDeleteModal(id) {
      this.productToDelete = id;
      this.deleteModal = true;
    },

    saveProduct() {
      axios.post("/api/products", this.newProduct)
        .then(() => {
          this.loadProducts();
          this.createModal = false;
        })
        .catch(err => alert("Ошибка: " + (err.response?.data?.message ?? err.message)));
    },

    updateProduct() {
      axios.put(`/api/products/${this.editProductData.id}`, this.editProductData)
        .then(() => {
          this.loadProducts();
          this.editModal = false;
        })
        .catch(err => alert("Ошибка: " + (err.response?.data?.message ?? err.message)));
    },

    confirmDelete() {
      axios.delete(`/api/products/${this.productToDelete}`)
        .then(() => {
          this.loadProducts();
          this.deleteModal = false;
        })
        .catch(err => alert("Ошибка: " + (err.response?.data?.message ?? err.message)));
    },
  },
};
</script>