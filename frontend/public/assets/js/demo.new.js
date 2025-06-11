(function() {
  'use strict';

  // Initialize PC global object to prevent undefined errors
  window.PC = window.PC || {};
  window.PC.plat = window.PC.plat || {};

  // Empty analytics placeholder to prevent errors
  window.PC.track = function() {};
  window.PC.plat.track = function() {};

  // Clean shutdown of tracking
  window.addEventListener('unload', function() {
    if (window.PC && window.PC.plat) {
      window.PC.plat = null;
    }
  });

  // Initialize essential UI components only if they exist
  document.addEventListener('DOMContentLoaded', function() {
    // Statistics chart
    var statsEl = document.getElementById('statisticsChart');
    if (statsEl && window.Chart) {
      var ctx = statsEl.getContext('2d');
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
          datasets: [{
            label: "Activity",
            borderColor: '#177dff',
            pointBackgroundColor: 'rgba(23, 125, 255, 0.6)',
            pointRadius: 0,
            backgroundColor: 'rgba(23, 125, 255, 0.4)',
            legendColor: '#177dff',
            fill: true,
            borderWidth: 2,
            data: [542, 480, 430, 550, 530, 453]
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          legend: {
            display: false
          },
          tooltips: {
            mode: "nearest",
            intersect: 0,
          },
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true
              }
            }]
          }
        }
      });
    }
  });
})();
