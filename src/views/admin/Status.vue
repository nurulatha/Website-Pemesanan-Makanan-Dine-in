<template>
  <div class="relative flex flex-col min-w-0 break-words w-full mb-6 mt-3">
    <div class="block w-full overflow-x-auto">
      <button class="bg-gray-200 shadow hover:shadow-lg p-3 mb-2" @click="createTable"><i class="fa-solid fa-plus mr-2"></i>Add Tables</button>
      <table class="items-center w-full bg-white border-collapse mt-6">
        <thead>
          <tr class="bg-gray-200 border-b border-gray-200">
            <th class="border border-slate-300 p-2">No</th>
            <th class="border border-slate-300 p-2">Table ID</th>
            <th class="border border-slate-300 p-2">URL</th>
            <th class="border border-slate-300 p-2">Status</th>
            <th class="border border-slate-300 p-2">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(table, index) in tables" :key="table.id" class="border-b border-gray-200">
            <td class="border border-slate-300 p-2">{{ index + 1 }}</td>
            <td class="border border-slate-300 p-2">{{ table.id }}</td>
            <td class="border border-slate-300 p-2">{{ table.url }}</td>
            <td class="border border-slate-300 p-2">
              <!-- Display status name -->
              {{ getStatusName(table.table_status_id) }}

              <!-- Dropdown for selecting status -->
              <select v-model="table.table_status_id" class="ml-3">
                <option v-for="status in tableStatuses" :key="status.id" :value="status.id">
                  {{ status.name }}
                </option>
              </select>

              <!-- Button for updating table status -->
              <button @click="updateTableStatus(table)" class="text-blue-500 hover:text-blue-700 ml-5 mr-3">
                <i class="fa-solid fa-pen-to-square ml-3"></i>
              </button>
            </td>
            <td class="border border-slate-300 p-2 text-center">
              <button @click="removeItem(table)" class="text-red-500 hover:text-red-700">
                <i class="fa-solid fa-trash"></i>
              </button>
            </td>
          </tr>
          <tr v-if="tables.length === 0">
            <td colspan="5" class="border border-slate-300 p-2 text-center">No tables found.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      tables: [],
      tableStatuses: [],
    };
  },
  created() {
    this.getTables();
    this.getTableStatuses();
  },
  methods: {
    // Fungsi untuk mengambil data tabel
    getTables() {
      axios
        .get(`${this.$apiURL}/api/tables`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
            "ngrok-skip-browser-warning": "69420",
          },
        })
        .then((response) => {
          // Tambahkan properti selectedStatus ke setiap meja
          this.tables = response.data.data.map((table) => ({
            ...table,
            selectedStatus: table.table_status_id, // Set initial status
          }));
        })
        .catch((error) => {
          console.error("Error fetching tables:", error);
          this.$toast.error("Failed to fetch tables");
        });
    },

    // Fungsi untuk mengambil status meja
    getTableStatuses() {
      axios
        .get(`${this.$apiURL}/api/table-statuses`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
            "ngrok-skip-browser-warning": "69420",
          },
        })
        .then((response) => {
          this.tableStatuses = response.data.data;
        })
        .catch((error) => {
          console.error("Error fetching table statuses:", error);
          this.$toast.error("Failed to fetch table statuses");
        });
    },

    // Fungsi untuk mendapatkan nama status dari table_status_id
    getStatusName(statusId) {
      const status = this.tableStatuses.find((status) => status.id === statusId);
      return status ? status.name : "Unknown Status";
    },

    // Fungsi untuk mengedit status meja
    updateTableStatus(table) {
    if (!table.table_status_id) {
      this.$toast.error("Please select a status");
      return;
    }
    axios
      .put(
        `${this.$apiURL}/api/tables/${table.id}`,
        {
          table_status_id: table.table_status_id,
        },
        {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
            "ngrok-skip-browser-warning": "69420",
          },
        }
      )
      .then(() => {
        this.$toast.success("Table status updated successfully");
        this.getTables(); 
      })
      .catch((error) => {
        console.error("Error updating table status:", error);
        this.$toast.error("Failed to update table status");
      });
  },

    // Fungsi untuk menghapus meja
    removeItem(table) {
      if (confirm(`Are you sure you want to delete table ${table.id}?`)) {
        axios
          .delete(`${this.$apiURL}/api/tables/${table.id}`, {
            headers: {
              Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
              "ngrok-skip-browser-warning": "69420",
            },
          })
          .then(() => {
            this.getTables();
          })
          .catch((error) => {
            console.error("Error deleting item:", error);
          });
      }
    },

    // Fungsi untuk membuat meja baru
    createTable() {
      if (confirm(`Are you sure you want to create new table?`)) {
        axios
          .post(
            `${this.$apiURL}/api/tables`,
            {
              url: "", // Mengirimkan data dalam bentuk JSON biasa
            },
            {
              headers: {
                Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
                "ngrok-skip-browser-warning": "69420",
              },
            }
          )
          .then((response) => {
            this.tables.push({
              ...response.data.data,
              selectedStatus: response.data.data.table_status_id,
            });
            this.$toast.success("Table created successfully");
          })
          .catch((error) => {
            console.error("Error creating table:", error);
            this.$toast.error("Failed to create table");
          });
      }
    },
  },
};
</script>
