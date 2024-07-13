<template>
  <div class="page-content__block">
    <div class="header-block">Ricerca ordini</div>
    <div class="container__inner">
      <h1 class="page-title">Ricerca Ordini per Email</h1>
      <form class="form-container" @submit.prevent="fetchOrders">
        <div class="form-group" is_mandatory="true">
          <label for="email" class="control-label">Email</label>
          <input v-model="email" type="email" name="email" class="form-control" placeholder="Inserisci email" />
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-primary btn-form-last" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span v-if="loading"> Caricamento...</span>
            <span v-else> Cerca Ordini</span>
          </button>
        </div>
        <div v-if="error" class="alert alert-danger">{{ error }}</div>
      </form>
      <div v-if="user && orders.length" class="orders-section">
        <p class="orders-title">
          Ordini trovati per l'utente {{ user.name }} ({{ user.email }})
        </p>
        <table class="table table-striped orders-table">
          <thead>
            <tr>
              <th v-for="(header, index) in headers" :key="index">{{ header }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(order, index) in orders" :key="index">
              <td v-for="header in headers" :key="header">{{ order[header] }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <hr />
    </div>
  </div>
</template>

<script>
export default {
  name: "EmailOrderComponent",
  props: {
    apiEndpoint: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      email: "",
      user: null,
      orders: [],
      headers: [],
      error: "",
      loading: false,
    };
  },
  methods: {
    async fetchOrders() {
      if (!this.email) {
        this.error = "Il campo email non può essere vuoto!";
        this.orders = [];
        this.user = null;
        return;
      }
      this.error = "";
      this.loading = true;
      try {
        const response = await fetch(`${this.apiEndpoint}?email=${this.email}`);
        const data = await response.json();
        if (data.error) {
          this.error = data.error;
          this.orders = [];
          this.user = null;
        } else {
          this.user = data.user;
          this.orders = data.orders || [];
          if (this.orders.length > 0) {
            this.headers = Object.keys(this.orders[0]);
          }
        }
      } catch (error) {
        this.error = "Errore durante il recupero degli ordini";
        console.error("Errore durante il recupero degli ordini:", error);
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
@import url('https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css');

.orders-table,
.orders-title {
  font-size: 14px;
}

@media (min-width: 768px) {
  .page-content__block {
    border-top: 1px solid #bcbaba;
    background: #fff;
    border-right: 1px solid #bcbaba;
    border-bottom: 1px solid #bcbaba;
    border-left: 1px solid #bcbaba;
    min-height: 500px;
  }

  .page-title {
    font-size: 33px;
    margin-bottom: 26px;
    font-weight: bold;
    line-height: 40px;
  }

  .container__inner {
    margin: 0 auto;
    padding: 25px 140px 40px 140px;
  }

  .orders-table,
  .orders-title {
    font-size: 18px;
  }
}

.page-content__block {
  background: #fff;
  min-height: 400px;
}

.container__inner {
  padding: 12px;
}

.page-title {
  font-size: 24px;
  margin-bottom: 18px;
  font-weight: bold;
  line-height: 26px;
}

label {
  font-size: 18px;
  font-weight: normal;
  line-height: 28px;
}

.form-control {
  background-color: #eae8e8;
  border-radius: 0;
  border: 1px solid #eae8e8;
  padding: 10px 10px;
  font-size: 18px;
  line-height: 26px;
  box-shadow: none;
  -webkit-box-shadow: none;
  height: auto;
}

.btn-primary,
.btn-primary:hover {
  background-color: #007bb5;
  border-color: #007bb5;
  box-shadow: none;
  color: #fff;
}

.spinner-border {
  margin-right: 5px;
}

.table {
  margin-top: 20px;
}

.header-block {
  display: block;
  padding: 0px 12px;
  background: #363535;
  height: 46px;
  width: 100%;
  color: #fff;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 16px;
  letter-spacing: 1px;
  line-height: 46px;
}

.form-container {
  margin-bottom: 20px;
}

.btn-form-last {
  width: 100%;
}

.alert-danger {
  margin-top: 20px;
}

.orders-section {
  margin-top: 20px;
  overflow-x: scroll;
}
</style>
