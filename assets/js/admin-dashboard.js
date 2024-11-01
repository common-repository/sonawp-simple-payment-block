jQuery(document).ready(function ($) {
  const ctx = document.getElementById("myChart");
  const config = {
      type: "bar",
      data: {
          labels: [],
          datasets: [
              {
                  label: "Amount",
                  data: [],
                  backgroundColor: ["rgba(54, 162, 235, 0.2)"],
                  borderColor: ["rgb(54, 162, 235)"],
                  borderWidth: 1,
              },
          ],
      },
      options: {
          scales: {
              y: {
                  beginAtZero: true,
              },
          },
      },
  };
  const myChart = new Chart(ctx, config);

  // Function to generate data for the last 7 days
  function generateLast7DaysData(data) {
      const currentDate = new Date();
      const last7Days = data; // Assuming data is an object with the 'last7Days' property
      const labels = [];

      for (let i = 6; i >= 0; i--) {
          const date = new Date(currentDate);
          date.setDate(currentDate.getDate() - i);
          labels.push(date.toLocaleDateString());
      }

      return { data: last7Days, labels: labels };
  }

  // Function to generate data for the last 30 days
  function generateLast30DaysData(data) {
      const currentDate = new Date();
      const last30Days = data.last30Days; // Assuming data is an object with the 'last30Days' property
      const labels = [];

      for (let i = 29; i >= 0; i--) {
          const date = new Date(currentDate);
          date.setDate(currentDate.getDate() - i);
          labels.push(date.toLocaleDateString());
      }

      return { data: last30Days, labels: labels };
  }

  // Function to generate data for the last 12 months
  function generateLast12MonthsData(data) {
      const currentDate = new Date();
      const last12Months = data.last12Months; // Assuming data is an object with the 'last12Months' property
      const labels = [];

      for (let i = 11; i >= 0; i--) {
          const date = new Date(currentDate);
          date.setMonth(currentDate.getMonth() - i);
          labels.push(
              date.toLocaleDateString("en-US", { month: "short", year: "numeric" })
          );
      }

      return { data: last12Months, labels: labels };
  }

  // Function to generate data for Month to Date
  function generateMonthToDateData(data) {
      const currentDate = new Date();
      const currentDay = currentDate.getDate();
      const monthToDate = data.monthToDate; // Assuming data is an object with the 'monthToDate' property
      const labels = [];

      for (let i = 0; i < currentDay; i++) {
          const date = new Date(currentDate);
          date.setDate(i + 1);
          labels.push(date.toLocaleDateString());
      }

      return { data: monthToDate, labels: labels };
  }

  // Function to generate data for Year to Date
  function generateYearToDateData(data) {
      const currentDate = new Date();
      const currentMonth = currentDate.getMonth();
      const yearToDate = data.yearToDate; // Assuming data is an object with the 'yearToDate' property
      const labels = [];

      for (let i = 0; i <= currentMonth; i++) {
          const date = new Date(currentDate);
          date.setMonth(i);
          labels.push(date.toLocaleDateString("en-US", { month: "short" }));
      }

      return { data: yearToDate, labels: labels };
  }

  // Function to update the chart with data
  function updateChartWithData(data) {
      const selectedValue = document.getElementById("filter").value;

      let chartData = [];
      let chartLabels = [];

      // Directly assign the data based on the selected filter
      if (selectedValue === "last7") {
          chartData = data;
          chartLabels = generateLast7DaysData(data).labels;
      } else if (selectedValue === "last30") {
          chartData = data;
          chartLabels = generateLast30DaysData(data).labels;
      } else if (selectedValue === "last12") {
          chartData = data;
          chartLabels = generateLast12MonthsData(data).labels;
      } else if (selectedValue === "month-to-date") {
          chartData = data;
          chartLabels = generateMonthToDateData(data).labels;
      } else if (selectedValue === "year-to-date") {
          chartData = data;
          chartLabels = generateYearToDateData(data).labels;
      }

      // Update the chart data and labels
      myChart.data.labels = chartLabels;
      myChart.data.datasets[0].data = chartData;

      // Update the chart
      myChart.update();
  }

  // Function to fetch data from PHP using AJAX
  function fetchData(filter, currency) {
    // console.log(currency);
    // console.log(filter);
      $.ajax({
          url: ajax_object.url,
          type: "post",
          data: {
              action: "order_chart",
              filter: filter,
              currency: currency, // Add currency parameter
              sona_ajax_order_chart_nonce: ajax_object.sona_ajax_order_chart_nonce,
          },
          success: function (response) {
              if (response.success) {
                  const data = response.data;
                  updateChartWithData(data);
              } else {
                  console.error("Server returned an error:", response.data);
              }
          },
          error: function (error) {
              console.log("Error:", error);
          },
      });
  }

  // Set the default data for "Last 7 Days" and "USD" on page load
  fetchData("last7", "usd");

  // Add event listeners to the select elements
  const filterSelect = $("#filter");
  const currencySelect = $("#currency");

  filterSelect.on("change", () => {
      const selectedValue = filterSelect.val();
      const selectedCurrency = currencySelect.val();
      fetchData(selectedValue, selectedCurrency);
  });

  currencySelect.on("change", () => {
      const selectedValue = filterSelect.val();
      const selectedCurrency = currencySelect.val();
      fetchData(selectedValue, selectedCurrency);
  });
});