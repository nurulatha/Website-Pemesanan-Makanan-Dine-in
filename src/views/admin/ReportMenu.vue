<template>
  <div class="block w-full overflow-x-auto">
    <h1 class="text-xl text-black">Report Sales Menu</h1>
    <div class="flex mt-5">
      <input type="date" v-model="startDate" />
      <input type="date" v-model="endDate" class="mr-3" />
      <button @click="fetchMenuReport" class="px-6 text-white rounded bg-emerald-500 hover:shadow-lg">Search</button>
      <button @click="exportToExcel" class="px-6 ml-3 bg-transparent border-2 rounded text-emerald-600 hover:shadow-lg" title="download excel">Excel</button>
      <button @click="exportToPDF" class="px-6 text-red-500 bg-transparent border-2 rounded hover:shadow-lg" title="download pdf">PDF</button>
    </div>
    <table id="salesTable" class="items-center w-full mt-6 bg-white border-collapse">
      <thead>
        <tr class="bg-gray-200 border-b border-gray-200">
          <th class="p-2 border border-slate-300">No</th>
          <th class="p-2 border border-slate-300">Name</th>
          <th class="p-2 border border-slate-300">Category</th>
          <th class="p-2 border border-slate-300">Price</th>
          <th class="p-2 border border-slate-300">Quantity's Sale</th>
          <th class="p-2 border border-slate-300">Total Sales</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(report, index) in reports" :key="report.menu_id">
          <td class="p-2 border border-slate-300">{{ index + 1 }}</td>
          <td class="p-2 border border-slate-300">{{ report.menu_name }}</td>
          <td class="p-2 border border-slate-300">{{ report.category_name }}</td>
          <td class="p-2 border border-slate-300">Rp {{ report.menu_price }}</td>
          <td class="p-2 border border-slate-300">{{ report.total_quantity }}</td>
          <td class="p-2 border border-slate-300">Rp {{ report.total_sales }}</td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="5" class="p-2 font-bold text-right border border-slate-300">Total:</td>
          <td class="p-2 border border-slate-300"></td>
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
    exportToExcel() {
      const params = {};
      if (this.startDate) {
        params.start_date = this.startDate;
      }
      if (this.endDate) {
        params.end_date = this.endDate;
      }

      axios
        .get(`${this.$apiURL}/api/reports/menus/excel`, {
          headers: {
            "ngrok-skip-browser-warning": "69420",
            Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
          },
          params: params,
          responseType: "blob", // Mengunduh file sebagai Blob (Excel file)
        })
        .then((response) => {
          // Buat URL object untuk file Excel
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "menu_sales_report.xlsx"); // Nama file yang diunduh
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        })
        .catch((error) => {
          console.error("Error exporting to Excel:", error);
        });
    },

    exportToPDF() {
      const params = {};
      if (this.startDate) {
        params.start_date = this.startDate;
      }
      if (this.endDate) {
        params.end_date = this.endDate;
      }

      axios
        .get(`${this.$apiURL}/api/reports/menus/pdf`, {
          headers: {
            "ngrok-skip-browser-warning": "69420",
            Authorization: `Bearer ${localStorage.getItem("auth_token")}`,
          },
          params: params,
          responseType: "blob", // Mengunduh file sebagai Blob (PDF file)
        })
        .then((response) => {
          // Buat URL object untuk file PDF
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "menu_sales_report.pdf"); // Nama file yang diunduh
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        })
        .catch((error) => {
          console.error("Error exporting to PDF:", error);
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
