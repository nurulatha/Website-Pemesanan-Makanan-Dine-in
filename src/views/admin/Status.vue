<template>
  <div class="relative flex flex-col w-full min-w-0 mt-3 mb-6 break-words">
    <div class="block w-full overflow-x-auto">
      <button class="p-3 mb-2 bg-gray-200 shadow hover:shadow-lg" @click="createTable"><i class="mr-2 fa-solid fa-plus"></i>Add Tables</button>
      <table class="items-center w-full mt-6 bg-white border-collapse">
        <thead>
          <tr class="bg-gray-200 border-b border-gray-200">
            <th class="p-2 border border-slate-300">No</th>
            <th class="p-2 border border-slate-300">Table ID</th>
            <th class="p-2 border border-slate-300">URL</th>
            <th class="p-2 border border-slate-300">Status</th>
            <th class="p-2 border border-slate-300">QR Code</th>
            <th class="p-2 border border-slate-300">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(table, index) in tables" :key="table.id" class="border-b border-gray-200">
            <td class="p-2 border border-slate-300">{{ index + 1 }}</td>
            <td class="p-2 border border-slate-300">{{ table.id }}</td>
            <td class="p-2 border border-slate-300">{{ table.url }}</td>
            <td class="p-2 border border-slate-300">
              <!-- Display status name -->
              {{ getStatusName(table.table_status_id) }}

              <!-- Dropdown for selecting status -->
              <select v-model="table.table_status_id" class="ml-3">
                <option v-for="status in tableStatuses" :key="status.id" :value="status.id">
                  {{ status.name }}
                </option>
              </select>

              <!-- Button for updating table status -->
              <button @click="updateTableStatus(table)" class="ml-5 mr-3 text-blue-500 hover:text-blue-700">
                <i class="ml-3 fa-solid fa-pen-to-square"></i>
              </button>
            </td>
            <td class="p-2 border border-slate-300">
              <img v-if="table.qr_code" :src="table.qr_code" alt="QR Code" class="w-16 h-16" />
            </td>
            <td class="p-2 text-center border border-slate-300">
              <button @click="removeItem(table)" class="text-red-500 hover:text-red-700">
                <i class="fa-solid fa-trash"></i>
              </button>
            </td>
          </tr>
          <tr v-if="tables.length === 0">
            <td colspan="5" class="p-2 text-center border border-slate-300">No tables found.</td>
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
            selectedStatus: table.table_status_id,
            qr_code: null,
          }));
          this.tables.forEach((table) => {
            if (!table.qr_code && table.url) {
              this.generateQRCode(table);
            }
          });
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

    //fungsi untuk generate qr code

    generateQRCode(table) {
      if (table.qr_code) return;
      if (!table.url) {
        this.$toast.error("URL not available for this table");
        return;
      }
      axios
        .post(
          `${this.$apiURL}/api/generate-qrcode`,
          { url: `${this.$URLQR}${table.url}` },
          {
            headers: {
              "ngrok-skip-browser-warning": "69420",
            },
          }
        )
        .then((response) => {
          // Tambahkan QR Code ke tabel
          table.qr_code = response.data.qr_code;
        })
        .catch((error) => {
          console.error("Error generating QR Code:", error);
          this.$toast.error("Failed to generate QR Code");
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
