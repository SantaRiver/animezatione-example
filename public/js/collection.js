var ctx = document.getElementById('myChart').getContext('2d');
const labels = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
];
const data = {
    labels: labels,
    datasets: [
        {
            label: 'Price',
            backgroundColor: '#2ee3fc',
            borderColor: '#2ee3fc',
            data: [0, 10, 5, 2, 20, 30, 45],
        },
        {
            label: 'Sales',
            backgroundColor: '#fae442',
            borderColor: '#fae442',
            data: [8, 1, 36, 8, 10, 6, 8],
        }
    ]
};
var myChart = new Chart(ctx, {
    type: 'line',
    data: data,
    options: {}
});


