<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Monitoring Pintu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-4 text-center">Monitoring Keamanan Pintu IoT</h1>
        
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="py-2 px-4 border">ID</th>
                        <th class="py-2 px-4 border">Jarak Sensor (cm)</th>
                        <th class="py-2 px-4 border">Status</th>
                        <th class="py-2 px-4 border">Waktu</th>
                    </tr>
                </thead>
                <tbody id="data-table" class="text-center">
                    <tr>
                        <td colspan="4" class="py-4">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function fetchData() {
            // Memanggil API route yang sudah kamu buat di routes/api.php
            fetch('/api/monitoring')
                .then(response => response.json())
                .then(data => {
                    let rows = '';
                    if(data.length === 0) {
                        rows = '<tr><td colspan="4" class="py-4">Belum ada data</td></tr>';
                    } else {
                        data.forEach(item => {
                            // Memberi warna merah jika "ADA ORANG"
                            let statusColor = item.status === 'ADA ORANG' ? 'text-red-600 font-bold' : 'text-green-600 font-bold';
                            
                            // Format tanggal
                            let date = new Date(item.created_at).toLocaleString('id-ID');

                            rows += `
                                <tr class="hover:bg-gray-50 border-b">
                                    <td class="py-2 px-4">${item.id}</td>
                                    <td class="py-2 px-4">${item.jarak} cm</td>
                                    <td class="py-2 px-4 ${statusColor}">${item.status}</td>
                                    <td class="py-2 px-4">${date}</td>
                                </tr>
                            `;
                        });
                    }
                    document.getElementById('data-table').innerHTML = rows;
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        // Jalankan fungsi fetchData pertama kali
        fetchData();

        // Ulangi ambil data setiap 3 detik (3000 ms) agar Real-Time
        setInterval(fetchData, 3000);
    </script>
</body>
</html>