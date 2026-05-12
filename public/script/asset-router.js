export function relayPage(pageName, data = {}) {  // Appends data in URL search parameters before going to next page 
  const params = new URLSearchParams(window.location.search); // Preserves old data
  
  params.set("page", pageName); // Rewrites page value
  
  for (const [key, val] of Object.entries(data)) {
    if (Array.isArray(val)) {
      params.delete(key);
      val.forEach(elem => params.append(key, elem));
    }
    else {
      params.set(key, val);
    }
  }
  
  window.location.href = `${window.location.origin}/index.php?${params.toString()}`;
}
