(() => {
 const canvas = document.getElementById('investmentChart');
 if (!canvas || typeof Chart === 'undefined') return;
 const data = Array.isArray(window.INVESTMENT_DATA) ? window.INVESTMENT_DATA : [];
 const currencySelect = document.getElementById('chartCurrency');
 const rangeSelect = document.getElementById('chartRange');
 let chart;
 const palette = ['#2457d6','#0f9d78','#c27824','#a33c91'];
 function draw() {
   const currency = currencySelect.value;
   const range = rangeSelect.value;
   const cutoff = range === 'all' ? null : new Date(Date.now() - Number(range) * 86400000);
   const filtered = data.filter(item => {
     const date = new Date(item.date + 'T00:00:00');
     return (currency === 'ALL' || item.currency === currency) && (!cutoff || date >= cutoff);
   });
   const currencies = [...new Set(filtered.map(item => item.currency))].sort();
   const labels = [...new Set(filtered.map(item => item.date))].sort();
   const datasets = currencies.map((code, index) => {
     const amountsByDate = {};
     filtered.filter(item => item.currency === code).forEach(item => {
       amountsByDate[item.date] = (amountsByDate[item.date] || 0) + item.amount;
     });
     return {
       label: code,
       data: labels.map(date => amountsByDate[date] ?? null),
       borderColor: palette[index % palette.length],
       backgroundColor: palette[index % palette.length] + '22',
       tension: 0.25,
       spanGaps: true
     };
   });
   if (chart) chart.destroy();
   chart = new Chart(canvas, {
     type: 'line',
     data: { labels, datasets },
     options: {
       responsive: true, maintainAspectRatio: false,
       interaction: { mode: 'index', intersect: false },
       plugins: { legend: { display: true, position: 'bottom' } },
       scales: {
         x: { title: { display: true, text: 'Submission date' } },
         y: { beginAtZero: true, title: { display: true, text: 'Submitted amount (currency-specific)' } }
       }
     }
   });
 }
 currencySelect.addEventListener('change', draw);
 rangeSelect.addEventListener('change', draw);
 draw();
})();
