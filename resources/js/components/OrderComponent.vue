<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
      <h2>Мои заказы</h2>
      <button class="btn btn-success" @click="createModal = true">
        <i class="fa-solid fa-plus"></i> Создать новый заказ
      </button>
    </div>

    <table class="table">
  <thead>
    <tr>
      <th>№</th><th>Клиент</th><th>Телефон</th><th>Товары</th><th>Итого</th><th>Действия</th>
    </tr>
  </thead>
  <tbody>
    <tr v-for="(order, index) in orders" :key="order.id">
      <td>{{ index + 1 }}</td>
      <td>{{ order.customer_name }}</td>
      <td>{{ order.customer_phone }}</td>
      <td>
        <span v-for="prod in order.products" :key="prod.id" class="badge bg-info me-1">
          {{ prod.name }} ({{ prod.pivot.quantity }} шт.)
        </span>
      </td>
      <td>{{ order.total_price }} сом</td>
      <td>
        <button class="btn btn-sm btn-primary me-2" @click="openEditModal(order)">Изменить</button>
        <button class="btn btn-sm btn-danger" @click="showDeleteModal(order.id)">Удалить</button>
      </td>
    </tr>
    <tr v-if="orders.length === 0">
      <td colspan="6" class="text-center">Заказы не найдены</td>
    </tr>
  </tbody>
</table>

    <MDBModal v-model="createModal" tabindex="-1">
      <MDBModalHeader><MDBModalTitle>Новый заказ</MDBModalTitle></MDBModalHeader>
      <MDBModalBody>
        <input class="form-control mb-2" v-model="newOrder.customer_name" placeholder="Имя клиента">
        <input class="form-control mb-2" v-model="newOrder.customer_phone" placeholder="Телефон">
        <label>Товары:</label>
        <div v-for="(item, index) in newOrder.items" :key="index" class="d-flex mb-2 gap-2">
          <select class="form-control" v-model="item.id">
            <option value="" disabled>Выберите товар</option>
            <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }}</option>
          </select>
          <input type="number" min="1" class="form-control w-25" v-model.number="item.quantity">
          <button class="btn btn-sm btn-danger" @click="newOrder.items.splice(index, 1)">x</button>
        </div>
        <button class="btn btn-sm btn-info" @click="newOrder.items.push({ id: '', quantity: 1 })">+ Товар</button>
      </MDBModalBody>
      <MDBModalFooter>
        <button class="btn btn-secondary" @click="createModal = false">Закрыть</button>
        <button class="btn btn-primary" @click="saveOrder">Сохранить</button>
      </MDBModalFooter>
    </MDBModal>

    <MDBModal v-model="editModal" tabindex="-1">
      <MDBModalHeader class="bg-primary text-white"><MDBModalTitle>Изменить заказ</MDBModalTitle></MDBModalHeader>
      <MDBModalBody>
        <input class="form-control mb-2" v-model="editOrder.customer_name">
        <input class="form-control mb-2" v-model="editOrder.customer_phone">
        <label>Товары:</label>
        <div v-for="(item, index) in editOrder.items" :key="index" class="d-flex mb-2 gap-2">
          <select class="form-control" v-model="item.id">
            <option value="" disabled>Выберите товар</option>
            <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }}</option>
          </select>
          <input type="number" min="1" class="form-control w-25" v-model.number="item.quantity">
          <button class="btn btn-sm btn-danger" @click="editOrder.items.splice(index, 1)">x</button>
        </div>
        <button class="btn btn-sm btn-info" @click="editOrder.items.push({ id: '', quantity: 1 })">+ Товар</button>
      </MDBModalBody>
      <MDBModalFooter>
        <button class="btn btn-secondary" @click="editModal = false">Закрыть</button>
        <button class="btn btn-primary" @click="updateOrder">Обновить</button>
      </MDBModalFooter>
    </MDBModal>

    <MDBModal v-model="deleteModal" tabindex="-1">
      <MDBModalHeader><MDBModalTitle>Удаление</MDBModalTitle></MDBModalHeader>
      <MDBModalBody>Вы действительно хотите удалить этот заказ?</MDBModalBody>
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
      orders: [],
      products: [],
      createModal: false,
      editModal: false,
      deleteModal: false,
      newOrder: { customer_name: '', customer_phone: '', items: [] },
      editOrder: { customer_name: '', customer_phone: '', items: [] },
      orderToDelete: null,
    };
  },

  mounted() {
    this.loadOrders();
    this.loadProducts();
     window.Echo.channel('orders')
        .listen('.order.created', () => {
            this.loadOrders();
        });
  },

  methods: {
    loadOrders() {
      axios.get("/api/orders").then(res => this.orders = res.data);
    },
    loadProducts() {
      axios.get("/api/products").then(res => this.products = res.data);
    },

    saveOrder() {
      axios.post("/api/orders", {
        customer_name:  this.newOrder.customer_name,
        customer_phone: this.newOrder.customer_phone,
        // Теперь передаём items с id и quantity
        items: this.newOrder.items.map(i => ({ id: i.id, quantity: i.quantity })),
      })
      .then(() => {
        this.loadOrders();
        this.createModal = false;
        this.newOrder = { customer_name: '', customer_phone: '', items: [] };
      })
      .catch(err => alert("Ошибка при сохранении: " + (err.response?.data?.message ?? err.message)));
    },

    openEditModal(order) {
      // Конвертирую products (с pivot) в единый формат items { id, quantity }
      this.editOrder = {
        id:             order.id,
        customer_name:  order.customer_name,
        customer_phone: order.customer_phone,
        items: order.products.map(p => ({
          id:       p.id,
          quantity: p.pivot.quantity,
        })),
      };
      this.editModal = true;
    },

    updateOrder() {
      axios.put(`/api/orders/${this.editOrder.id}`, {
        customer_name:  this.editOrder.customer_name,
        customer_phone: this.editOrder.customer_phone,
        items: this.editOrder.items.map(i => ({ id: i.id, quantity: i.quantity })),
      })
      .then(() => {
        this.loadOrders();
        this.editModal = false;
      })
      .catch(err => console.error(err.response?.data ?? err.message));
    },

    showDeleteModal(id) {
      this.orderToDelete = id;
      this.deleteModal = true;
    },
    confirmDelete() {
      axios.delete(`/api/orders/${this.orderToDelete}`)
        .then(() => {
          this.loadOrders();
          this.deleteModal = false;
        });
    },
  },
};
</script>