<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Monitoring Pintu IoT</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-4 text-center">Monitoring Keamanan Pintu IoT</h1>
        
        <div class="flex justify-between items-center bg-gray-50 p-4 rounded-md mb-6 border">
            <div>
                <p class="text-gray-700 font-semibold">Status Buzzer Alarm:</p>
                <p id="buzzer-status-text" class="text-xl font-bold text-green-600">ON</p>
            </div>
            <div>
                <button id="btn-toggle" onclick="toggleBuzzer()" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-300 disabled:opacity-50">
                    Matikan Buzzer (20 Menit)
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="py-2 px-4 border">ID</th>
                        <th class="py-2 px-4 border">Jarak (cm)</th>
                        <th class="py-2 px-4 border">Status</th>
                        <th class="py-2 px-4 border">Waktu</th>
                    </tr>
                </thead>
                <tbody id="data-table" class="text-center">
                    <tr><td colspan="4" class="py-4 text-gray-500">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        let currentBuzzerState = 'ON';

        function fetchData() {
            fetch('/api/monitoring')
                .then(response => response.json())
                .then(res => {
                    // Update Status Buzzer di Layar
                    currentBuzzerState = res.buzzer_state;
                    let bText = document.getElementById('buzzer-status-text');
                    let bBtn = document.getElementById('btn-toggle');
                    
                    if(currentBuzzerState === 'OFF') {
                        bText.innerText = "MUTE (Mati)"; 
                        bText.className = "text-xl font-bold text-red-600";
                        bBtn.innerText = "Nyalakan Buzzer Sekarang"; 
                        bBtn.className = "bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-300";
                    } else {
                        bText.innerText = "ON (Aktif)"; 
                        bText.className = "text-xl font-bold text-green-600";
                        bBtn.innerText = "Matikan Buzzer (20 Menit)"; 
                        bBtn.className = "bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-300";
                    }

                    // Update Tabel
                    let rows = '';
                    if(!res.data || res.data.length === 0) {
                        rows = '<tr><td colspan="4" class="py-4 text-gray-500">Belum ada data</td></tr>';
                    } else {
                        res.data.forEach(item => {
                            let statusColor = item.status === 'ADA ORANG' ? 'text-red-600 font-bold' : 'text-green-600 font-bold';
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
                .catch(error => console.error('Gagal mengambil data:', error));
        }

        function toggleBuzzer() {
            // Mencegah klik tombol berkali-kali saat proses berjalan
            let btn = document.getElementById('btn-toggle');
            btn.disabled = true;
            btn.innerText = "Memproses...";

            let newState = currentBuzzerState === 'ON' ? 'OFF' : 'ON';
            
            fetch('/api/buzzer/toggle', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json' // Header penting agar Laravel tahu ini request API
                },
                body: JSON.stringify({ state: newState })
            })
            .then(response => response.json())
            .then(data => {
                console.log(data.message);
                fetchData(); // Refresh UI langsung setelah klik sukses
            })
            .catch(error => console.error('Gagal ubah status buzzer:', error))
            .finally(() => {
                btn.disabled = false; // Kembalikan tombol agar bisa diklik lagi
            });
        }

        // Panggil saat halaman pertama kali dibuka
        fetchData();
        
        // Auto-refresh setiap 3 detik (3000 milidetik)
        setInterval(fetchData, 3000); 
    </script>
</body>
</html>