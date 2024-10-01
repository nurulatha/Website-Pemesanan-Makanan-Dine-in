<template>
  <div class="blocl w-full overflow-x-auto">
    <h1 class="text-xl text-black">Report Table's Sale</h1>
    <div class="flex mt-5">
      <input type="date" v-model="startDate" />
      <input type="date" v-model="endDate" class="mr-3" />
      <button @click="fetchTableReport" class="bg-emerald-500 hover:shadow-lg text-white rounded px-6">Filter</button>
    </div>
    <table id="salesTable" class="items-center w-full bg-white border-collapse mt-6">
      <thead>
        <tr class="bg-gray-200 border">
          <th class="border border-slate-300 p-2">Table</th>
          <th class="border border-slate-300 p-2">Total Order</th>
          <th class="border border-slate-300 p-2">Sales</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th class="border border-slate-300 p-2">Total</th>
          <th class="border border-slate-300 p-2"></th>
          <!-- Placeholder untuk total orders -->
          <th class="border border-slate-300 p-2"></th>
          <!-- Placeholder untuk total sales jika dibutuhkan -->
        </tr>
      </tfoot>
      <tbody>
        <tr v-for="report in reports" :key="report.table_id" class="border border-slate-300">
          <td class="border border-slate-300 p-2">{{ report.table_id }}</td>
          <td class="border border-slate-300 p-2">{{ report.total_orders }} order</td>
          <td class="border border-slate-300 p-2">Rp {{ report.total_sales }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import axios from "axios";
import $ from "jquery";
import "datatables.net";

export default {
  data() {
    return {
      reports: [],
      startDate: null,
      endDate: null,
      dataTable: null,
    };
  },
  methods: {
    fetchTableReport() {
      const params = {};
      if (this.startDate) {
        params.start_date = this.startDate;
      }
      if (this.endDate) {
        params.end_date = this.endDate;
      }
      axios
        .get(`${this.$apiURL}/api/reports/tables`, {
          headers: {
            "ngrok-skip-browser-warning": "69420",
            Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
          },
          params: params,
        })
        .then((response) => {
          this.reports = response.data.data;
          this.initializeDataTable();
        })
        .catch((error) => {
          console.error("error fetching report", error);
        });
    },
    initializeDataTable() {
      // Destroy the previous instance if it exists
      if ($.fn.DataTable.isDataTable("#salesTable")) {
        $("#salesTable").DataTable().destroy();
      }

      // Initialize DataTable
      this.dataTable = $("#salesTable").DataTable({
        paging: true,
        searching: true,
        ordering: true,
        order: [[0, "asc"]],
        data: this.reports, // Use the fetched reports
        columns: [
          { data: "table_id" },
          {
            data: "total_orders",
            render: (data) => `${data} order`, // Menambahkan "order" setelah total_orders
          },
          {
            data: "total_sales",
            render: (data) => `Rp ${data}`,
          },
        ],
        createdRow: function (row) {
          // Adding custom classes for Tailwind CSS
          $(row).addClass("bg-white hover:bg-gray-100 text-md");
          $("td", row).addClass("border border-slate-300 p-2");
        },
        headerCallback: function (thead) {
          // Adding custom classes for Tailwind CSS
          $("th", thead).addClass("border border-slate-300 p-2");
        },
        // Tambahkan footerCallback untuk menghitung total orders
        footerCallback: function () {
          let api = this.api();

          // Calculate the total for the current page (visible rows)
          let totalOrders = api
            .column(1, { page: "current" })
            .data()
            .reduce((a, b) => a + parseInt(b), 0);

          let totalSales = api
            .column(2, { page: "current" }) // Kolom ke-2 (total_sales)
            .data()
            .reduce((a, b) => a + parseFloat(b), 0);

          // Update the footer
          $(api.column(1).footer()).html(`${totalOrders} orders`);
          $(api.column(2).footer()).html(`Rp ${totalSales.toLocaleString('id-ID')}`);
        },
      });
    },
  },
  mounted() {
    this.fetchTableReport();
  },
};
</script>
