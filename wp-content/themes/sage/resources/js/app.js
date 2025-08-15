document.addEventListener('DOMContentLoaded', () => {
  console.log("app.js loaded");
    const loadMoreBtn = document.getElementById("load-more");
    console.log(loadMoreBtn);
    
              if (!loadMoreBtn) return;
  
              loadMoreBtn.addEventListener("click", function () {
                  const button = this;
                  const page = parseInt(button.dataset.page);
  
                  const data = new FormData();
                  data.append("action", "load_more_projects");
                  data.append("paged", page);
  
                  fetch(filter_params.ajax_url,{
                      method: "POST",
                      body: data,
                  })
                      .then((res) => res.json())
                      .then((res) => {
                          if (res.success) {
                              const postsContainer = document.getElementById("project-posts");
                              postsContainer.insertAdjacentHTML("beforeend", res.data.html);
                              button.dataset.page = page + 1;
  
                              if (!res.data.has_more) {
                                  button.style.display = "none";
                              }
                          }
                      });
              });
    
  
  });
  
  

