const API_URL = 'ws://127.0.0.1:8000/ws/posts/'; // WebSocket connection for real-time updates
const roleSelector = document.getElementById('userRole');
const createPostSection = document.getElementById('createPostSection');
const postForm = document.getElementById('postForm');
const postsContainer = document.getElementById('postsContainer');

// Simulate role-based access
roleSelector.addEventListener('change', () => {
  const role = roleSelector.value;
  createPostSection.style.display = role === 'viewer' ? 'none' : 'block';
});

// Fetch posts from WebSocket and render them
const socket = new WebSocket(API_URL);
socket.onmessage = (event) => {
  const posts = JSON.parse(event.data);
  renderPosts(posts);
};

function renderPosts(posts) {
  postsContainer.innerHTML = ''; // Clear existing posts
  posts.forEach((post) => {
    const postElement = document.createElement('div');
    postElement.classList.add('col-md-4');
    postElement.innerHTML = `
      <div class="post">
        <h5>${post.title}</h5>
        <p><strong>Category:</strong> ${post.category}</p>
        <p>${post.content}</p>
        ${post.attachment ? `<img src="${post.attachment}" alt="Attachment">` : ''}
        <small>Posted on: ${new Date(post.created_at).toLocaleString()}</small>
        ${roleSelector.value !== 'viewer' ? `<button class="btn btn-danger btn-sm delete-post" data-id="${post.id}">Delete</button>` : ''}
      </div>
    `;
    postsContainer.appendChild(postElement);
  });
}

// Handle post submission
postForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  const formData = new FormData(postForm);
  const post = {
    title: formData.get('title'),
    category: formData.get('category'),
    content: formData.get('content'),
    attachment: formData.get('attachment'),
  };
  socket.send(JSON.stringify({ action: 'create', post }));
  postForm.reset();
});
