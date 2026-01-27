<!-- Blur Overlay -->
<div
  id="overlay"
  class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-40 transition"
></div>

<!-- Notification Wrapper -->
<div class="relative z-50 w-fit">

  <!-- Bell Icon Button -->
  <button
    type="button"
    onclick="toggleDropdown()"
    class="relative p-3 rounded-full bg-white shadow hover:bg-gray-100 transition"
  >
    🔔
    <span id="bell-dot" class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
  </button>

  <!-- Dropdown -->
  <div
    id="dropdown"
    class="hidden absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-2xl overflow-hidden"
  >
    <!-- Loading State -->
    <div id="loader" class="p-4 space-y-3 animate-pulse">
      <div class="h-4 bg-gray-300 rounded w-3/4"></div>
      <div class="h-3 bg-gray-200 rounded w-1/2"></div>
      <div class="h-4 bg-gray-300 rounded w-2/3"></div>
    </div>

    <!-- Notifications -->
    <div id="content" class="hidden max-h-80 overflow-y-auto divide-y">

      <!-- Mark all read button -->
      <div class="p-3 text-center border-b">
        <button
          onclick="markAllRead()"
          class="text-sm text-blue-600 hover:underline font-medium"
        >
          Mark all as read
        </button>
      </div>

      <!-- Notification Items -->
      <div id="notifications-container"></div>
    </div>

    <!-- Footer -->
    <div class="p-3 text-center border-t">
      <a
        href="/notifications"
        onclick="goToNotifications(event)"
        class="text-sm text-blue-600 hover:underline font-medium"
      >
        See all notifications
      </a>
    </div>
  </div>
</div>

<script>
  const notifications = [
    { icon: '💬', text: 'Someone replied to your post', read: false },
    { icon: '👍', text: 'Someone liked your answer', read: true },
    { icon: '👤', text: 'Someone started following you', read: false },
    { icon: '⚠️', text: 'Your post was reported', read: true },
  ];

  const container = document.getElementById('notifications-container');
  const bellDot = document.getElementById('bell-dot');
  const dropdown = document.getElementById('dropdown');
  const overlay = document.getElementById('overlay');
  const loader = document.getElementById('loader');
  const content = document.getElementById('content');

  function renderNotifications() {
    container.innerHTML = '';
    let hasUnread = false;

    notifications.forEach((notif, index) => {
      const div = document.createElement('div');
      div.className = `flex items-center gap-3 p-4 cursor-pointer transition notification ${notif.read ? '' : 'bg-blue-50 hover:bg-blue-100'}`;
      div.onclick = () => markRead(index);

      const icon = document.createElement('span');
      icon.className = 'text-xl';
      icon.innerText = notif.icon;

      const text = document.createElement('p');
      text.className = `flex-1 text-sm ${notif.read ? 'text-gray-700' : 'font-medium text-gray-800'} truncate`;
      text.innerText = notif.text;

      div.appendChild(icon);
      div.appendChild(text);

      if (!notif.read) {
        const dot = document.createElement('span');
        dot.className = 'w-2 h-2 bg-blue-500 rounded-full unread-dot';
        div.appendChild(dot);
        hasUnread = true;
      }

      container.appendChild(div);
    });

    bellDot.style.display = hasUnread ? 'block' : 'none';
  }

  function markRead(index) {
    notifications[index].read = true;
    renderNotifications();
  }

  function markAllRead() {
    notifications.forEach(n => n.read = true);
    renderNotifications();
  }

  function toggleDropdown() {
    dropdown.classList.toggle('hidden');
    overlay.classList.toggle('hidden');

    if (!dropdown.classList.contains('hidden')) {
      loader.classList.remove('hidden');
      content.classList.add('hidden');

      setTimeout(() => {
        loader.classList.add('hidden');
        content.classList.remove('hidden');
        renderNotifications();
      }, 500);
    }
  }

  function closeDropdown() {
    dropdown.classList.add('hidden');
    overlay.classList.add('hidden');
  }

  function goToNotifications(event) {
    event.preventDefault();
    closeDropdown();
    setTimeout(() => {
      window.location.href = '/notifications';
    }, 200);
  }

  // Close dropdown when clicking outside
  document.addEventListener('click', (e) => {
    if (!dropdown.contains(e.target) && !e.target.closest('button[onclick="toggleDropdown()"]')) {
      closeDropdown();
    }
  });

  // Initial render
  renderNotifications();
</script>
