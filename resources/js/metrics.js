import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', function() {
    const closeModalBtn = document.getElementById('closeMetricsModal');
    const metricsModal = document.getElementById('metricsModal');
    const totalMessagesEl = document.getElementById('totalMessages');
    const repeatedRecipientsEl = document.getElementById('repeatedRecipients');
    const noRepeatedRecipientsEl = document.getElementById('noRepeatedRecipients');
    const mostFrequentRecipientsEl = document.getElementById('mostFrequentRecipients');
    const noFrequentRecipientsEl = document.getElementById('noFrequentRecipients');
    const chartLegendEl = document.getElementById('chartLegend');
    let platformChart = null;
    const appDataDiv = document.getElementById('app-data');
    const messagesUserRouteBase = appDataDiv ? appDataDiv.dataset.messagesUserRoute : null;

    const userSelector = document.getElementById('user_selector');
    const loadUserMetricsBtn = document.getElementById('loadUserMetrics');

    if (!messagesUserRouteBase) {
        console.error('Messages user route data not found. Cannot fetch user metrics.');
        if (loadUserMetricsBtn) loadUserMetricsBtn.style.display = 'none';
        return;
    }

    if (!closeModalBtn || !metricsModal || !totalMessagesEl || !userSelector || !loadUserMetricsBtn) {
        console.error('One or more required modal or user selection elements not found. Check your HTML IDs.');
        if (loadUserMetricsBtn) loadUserMetricsBtn.style.display = 'none';
        return;
    }

    async function fetchAndDisplayMetrics(userId) {
        // console.log(`Fetching messages for user ID: ${userId}`);
        try {
            const url = messagesUserRouteBase.replace('PLACEHOLDER', userId);
            const response = await fetch(url);

            if (!response.ok) {
                if (response.status === 403) {
                    alert('No estás autorizado para ver las métricas de este usuario.');
                } else {
                    alert('Error al cargar las métricas. Inténtalo de nuevo.');
                }
                console.error('Failed to fetch messages:', response.statusText);
                return;
            }

            const messagesData = await response.json();
            // console.log('Messages Data fetched:', messagesData);

            updateMetrics(messagesData);
            metricsModal.classList.remove('hidden');

        } catch (error) {
            console.error('Error fetching messages:', error);
            alert('Ocurrió un error al cargar las métricas. Revisa la consola para más detalles.');
        }
    }

    function updateMetrics(messagesData) {
        // console.log('updateMetrics function called with data:', messagesData);
        const totalMessages = messagesData.length;
        totalMessagesEl.textContent = totalMessages;

        const platformCounts = {};
        const allRecipients = [];

        messagesData.forEach(msg => {
            platformCounts[msg.platform] = (platformCounts[msg.platform] || 0) + 1;
            try {
                const recipientsArray = Array.isArray(msg.recipients) ? msg.recipients : JSON.parse(msg.recipients || '[]');
                allRecipients.push(...recipientsArray);
            } catch (e) {
                console.error('Error parsing recipients for message ID ' + msg.id + ':', msg.recipients, e);
            }
        });

        const chartLabels = Object.keys(platformCounts);
        const chartData = Object.values(platformCounts);
        const backgroundColors = [
            'rgba(75, 192, 192, 0.6)', 'rgba(255, 99, 132, 0.6)', 'rgba(54, 162, 235, 0.6)',
            'rgba(255, 206, 86, 0.6)', 'rgba(153, 102, 255, 0.6)', 'rgba(255, 159, 64, 0.6)',
            'rgba(201, 203, 207, 0.6)'
        ];
        const borderColors = backgroundColors.map(color => color.replace('0.6', '1'));

        if (platformChart) {
            platformChart.destroy();
        }

        const ctx = document.getElementById('platformChart');
        if (ctx) {
            platformChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        data: chartData,
                        backgroundColor: backgroundColors.slice(0, chartLabels.length),
                        borderColor: borderColors.slice(0, chartLabels.length),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) label += ': ';
                                    if (context.parsed !== null) label += context.parsed + ' (' + ((context.parsed / totalMessages) * 100).toFixed(1) + '%)';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        } else {
            console.error('Canvas element with ID "platformChart" not found.');
        }

        if (chartLegendEl) {
            chartLegendEl.innerHTML = '';
            chartLabels.forEach((label, index) => {
                const legendItem = document.createElement('div');
                legendItem.className = 'flex items-center space-x-2';
                const colorBox = document.createElement('span');
                colorBox.className = 'w-4 h-4 rounded-full';
                colorBox.style.backgroundColor = backgroundColors[index];
                const text = document.createElement('span');
                text.textContent = `${label}: ${chartData[index]} (${((chartData[index] / totalMessages) * 100).toFixed(1)}%)`;
                legendItem.appendChild(colorBox);
                legendItem.appendChild(text);
                chartLegendEl.appendChild(legendItem);
            });
        }

        const recipientCounts = {};
        allRecipients.forEach(recipient => {
            if (recipient) recipientCounts[recipient] = (recipientCounts[recipient] || 0) + 1;
        });

        if (repeatedRecipientsEl) repeatedRecipientsEl.innerHTML = '';
        if (mostFrequentRecipientsEl) mostFrequentRecipientsEl.innerHTML = '';

        const repeated = Object.entries(recipientCounts).filter(([, count]) => count > 1);
        if (repeatedRecipientsEl) {
            if (repeated.length > 0) {
                if (noRepeatedRecipientsEl) noRepeatedRecipientsEl.classList.add('hidden');
                repeated.forEach(([recipient, count]) => {
                    const li = document.createElement('li');
                    li.textContent = `${recipient} (${count} veces)`;
                    repeatedRecipientsEl.appendChild(li);
                });
            } else {
                if (noRepeatedRecipientsEl) noRepeatedRecipientsEl.classList.remove('hidden');
            }
        }

        const sortedRecipients = Object.entries(recipientCounts).sort((a, b) => b[1] - a[1]);
        const top5Recipients = sortedRecipients.slice(0, 5);

        if (mostFrequentRecipientsEl) {
            if (top5Recipients.length > 0) {
                if (noFrequentRecipientsEl) noFrequentRecipientsEl.classList.add('hidden');
                top5Recipients.forEach(([recipient, count]) => {
                    const li = document.createElement('li');
                    li.textContent = `${recipient} (${count} mensajes)`;
                    mostFrequentRecipientsEl.appendChild(li);
                });
            } else {
                if (noFrequentRecipientsEl) noFrequentRecipientsEl.classList.remove('hidden');
            }
        }
    }


    loadUserMetricsBtn.addEventListener('click', function() {
        // console.log('Load User Metrics button clicked.');
        const selectedUserId = userSelector.value;
        fetchAndDisplayMetrics(selectedUserId);
    });

    closeModalBtn.addEventListener('click', function() {
        // console.log('Close Metrics button clicked.');
        metricsModal.classList.add('hidden');
    });

    metricsModal.addEventListener('click', function(e) {
        if (e.target === metricsModal) {
            // console.log('Clicked outside modal. Closing.');
            metricsModal.classList.add('hidden');
        }
    });

});
