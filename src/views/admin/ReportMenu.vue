<template>
  <div class="block w-full overflow-x-auto">
    <h1 class="text-xl text-black">Report Sales Menu</h1>
    <div class="flex mt-5">
      <input type="date" v-model="startDate" />
      <input type="date" v-model="endDate" class="mr-3" />
      <button @click="fetchMenuReport" class="bg-emerald-500 hover:shadow-lg text-white rounded px-6">Search</button>
    </div>
    <table id="salesTable" class="items-center w-full bg-white border-collapse mt-6">
      <thead>
        <tr class="bg-gray-200 border-b border-gray-200">
          <th class="border border-slate-300 p-2">No</th>
          <th class="border border-slate-300 p-2">Name</th>
          <th class="border border-slate-300 p-2">Category</th>
          <th class="border border-slate-300 p-2">Price</th>
          <th class="border border-slate-300 p-2">Quantity's Sale</th>
          <th class="border border-slate-300 p-2">Total Sales</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(report, index) in reports" :key="report.menu_id">
          <td class="border border-slate-300 p-2">{{ index + 1 }}</td>
          <td class="border border-slate-300 p-2">{{ report.menu_name }}</td>
          <td class="border border-slate-300 p-2">{{ report.category_name }}</td>
          <td class="border border-slate-300 p-2">Rp {{ report.menu_price }}</td>
          <td class="border border-slate-300 p-2">{{ report.total_quantity }}</td>
          <td class="border border-slate-300 p-2">Rp {{ report.total_sales }}</td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="5" class="border border-slate-300 p-2 text-right font-bold">Total:</td>
          <td class="border border-slate-300 p-2"></td>
        </tr>
      </tfoot>
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
    fetchMenuReport() {
      const params = {};
      if (this.startDate) {
        params.start_date = this.startDate;
      }
      if (this.endDate) {
        params.end_date = this.endDate;
      }
      axios
        .get(`${this.$apiURL}/api/reports/menus`, {
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
          console.error("Error fetching products:", error);
        });
    },
    initializeDataTable() {
      if ($.fn.DataTable.isDataTable("#salesTable")) {
        $("#salesTable").DataTable().destroy();
      }
      this.dataTable = $("#salesTable").DataTable({
        paging: true,
        searching: true,
        ordering: true,
        order: [[0, "asc"]],
        data: this.reports,
        columns: [
          { data: null, render: (data, type, row, meta) => meta.row + 1 },
          { data: "menu_name" },
          { data: "category_name" },
          { data: "menu_price", render: (data) => `Rp ${data}` },
          { data: "total_quantity" },
          { data: "total_sales", render: (data) => `Rp ${data}` },
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
        footerCallback: function () {
          let api = this.api();

          let totalSales = api
            .column(5, { page: "current" })
            .data()
            .reduce((a, b) => a + parseFloat(b), 0);

          // Update the footer
          $(api.column(5).footer()).html(`Rp ${totalSales.toLocaleString("id-ID")}`);
        },
      });
    },
  },

  mounted() {
    this.fetchMenuReport();
  },
};
</script>
