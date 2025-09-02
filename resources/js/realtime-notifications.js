// Initialize Echo
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true
});

// Notifikasi pengguna
const userId = document.querySelector('meta[name="user-id"]')?.content;

if (userId) {
    window.Echo.private(`user.notifications.${userId}`)
        .listen('.new.notification', (e) => {
            console.log('New notification received:', e);

            // Perbarui jumlah notifikasi
            updateNotificationCount();

            // Tampilkan toast notifikasi
            showNotificationToast(e);
        });
}

// Mendengarkan pembaruan tugas
const taskId = document.querySelector('meta[name="task-id"]')?.content;

if (taskId) {
    window.Echo.channel(`task.${taskId}`)
        .listen('.task.updated', (e) => {
            console.log('Task updated:', e);

            // Perbarui tampilan tugas
            if (typeof updateTaskView === 'function') {
                updateTaskView(e);
            } else {
                // Jika di halaman detail, mungkin ingin me-refresh halaman
                showTaskUpdateToast(e);
            }
        });
}

// Fungsi utilitas untuk notifikasi
function updateNotificationCount() {
    fetch('/notifications/count')
        .then(response => response.json())
        .then(data => {
            const countElement = document.getElementById('notification-count');
            if (countElement) {
                if (data.count > 0) {
                    countElement.textContent = data.count;
                    countElement.classList.remove('hidden');
                } else {
                    countElement.classList.add('hidden');
                }
            }
        });
}

function showNotificationToast(notification) {
    // Buat elemen toast
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 bg-blue-500 text-white p-4 rounded-lg shadow-lg max-w-md z-50 animate-slideIn';
    toast.style.transform = 'translateX(100%)';
    toast.innerHTML = `
        <div class="flex items-start">
            <div class="flex-1">
                <h4 class="font-bold">${notification.title}</h4>
                <p class="text-sm">${notification.message}</p>
            </div>
            <button class="ml-4 text-white" onclick="this.parentElement.parentElement.remove()">
                &times;
            </button>
        </div>
    `;

    // Tambahkan ke body
    document.body.appendChild(toast);

    // Animasi masuk
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 10);

    // Hilangkan setelah 5 detik
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 5000);
}

function showTaskUpdateToast(taskUpdate) {
    // Buat elemen toast
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 left-4 bg-green-500 text-white p-4 rounded-lg shadow-lg max-w-md z-50 animate-slideIn';
    toast.style.transform = 'translateY(100%)';

    let actionMessage = '';
    switch(taskUpdate.action) {
        case 'created':
            actionMessage = 'telah dibuat';
            break;
        case 'updated':
            actionMessage = 'telah diperbarui';
            break;
        case 'status_changed':
            actionMessage = 'status telah diperbarui';
            break;
        case 'needs_revision':
            actionMessage = 'memerlukan revisi';
            break;
        default:
            actionMessage = 'telah diperbarui';
    }

    toast.innerHTML = `
        <div class="flex items-start">
            <div class="flex-1">
                <h4 class="font-bold">Pembaruan Tugas</h4>
                <p class="text-sm">Tugas "${taskUpdate.title}" ${actionMessage}.</p>
                <p class="text-xs mt-1">${taskUpdate.updated_at}</p>
            </div>
            <button class="ml-4 text-white" onclick="this.parentElement.parentElement.remove()">
                &times;
            </button>
        </div>
    `;

    // Tambahkan ke body
    document.body.appendChild(toast);

    // Animasi masuk
    setTimeout(() => {
        toast.style.transform = 'translateY(0)';
    }, 10);

    // Hilangkan setelah 5 detik
    setTimeout(() => {
        toast.style.transform = 'translateY(100%)';
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 5000);
}

// Menambahkan CSS untuk animasi
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); }
        to { transform: translateX(0); }
    }
    .animate-slideIn {
        animation: slideIn 0.3s ease-out;
    }
`;
document.head.appendChild(style);
