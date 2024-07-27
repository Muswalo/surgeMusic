function toggleContent(button) {
  const articleContent = button.closest('.article').querySelector('.article-content');

  if (articleContent.classList.contains('expanded')) {
    // Collapse the content
    articleContent.classList.remove('expanded');
    button.innerHTML = '<i class="fas fa-plus"></i> Read More';
  } else {
    // Expand the content
    articleContent.classList.add('expanded');
    button.innerHTML = '<i class="fas fa-minus"></i> Read Less';
  }
}