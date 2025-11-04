const assetTable = document.querySelector(".asset-table");
const assetTableBody = assetTable.querySelector("tbody");

document.addEventListener("DOMContentLoaded", () => {
  addTableFuncs();
  assetTable.querySelector("thead tr").appendChild(document.createElement("th"));
  addAssetAdd();

});

assetTableBody.addEventListener("assetsLoaded", () => {
  // const assetForm = document.querySelector('.add-asset-form');
  const menuButtons = document.getElementsByClassName("menu-item");
  const multiSelectButton = document.getElementById("multi-select");
  const rows = assetTableBody.querySelectorAll("tr");
  // const actionButtons = document.getElementsByClassName("menu-item");
  
  let isMultiSelect = false;
  let selectedAll = false;
  
  addActionsButton();

  Array.from(menuButtons).forEach(btn => {
    btn.addEventListener("click", (e) => {
      const row = e.target.closest("tr");
      const cells = row.querySelectorAll("td");
      const propNum = cells[0].textContent.trim();
      switch (btn.id) {
        case "view-action":
          viewAsset(propNum);
          break;
        case "modify-action":
          editAssets(propNum);
          break;
        case "delete-action":
          deleteAssets(propNum);
          break;
        default:
          localStorage.setItem("assetsToAssign", JSON.stringify([propNum]));
          window.location.href = "../views/assign-user.php";
      }
    })
  })

  function viewAsset(filter){
		const src = "../handlers/fetch-asset.php";

		fetch(src, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `search=${filter}`,
    })
		.then(res => res.json())
		.then(data => {
			localStorage.setItem("viewAssetData", JSON.stringify(data));
			window.location.href = "../views/view-asset.php"
		})
	}
  
  function editAssets(filter){
    const src = "../handlers/fetch-asset.php";
  
    fetch(src, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `search=${filter}`,
    })
    .then(res => res.json())
    .then(data => {
      localStorage.setItem("assetData", JSON.stringify(data));
      window.location.href = "../views/edit-asset-form.php";
    })
    .catch(err => console.error("Error edit assets: ", err))
  }
  
  function deleteAssets(filter){
    const src = "../handlers/delete-asset.php";
    fetch(src, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `search=${filter}`,
    }).then(_ => {
      window.location.href = "../views/asset-manager.php";
    })
  }
  
  // function assignAssets(){
  // 	selectedAssets = getPropNumSelected();
  // 	console.log(selectedAssets);
  // 	if (selectedAssets.length != 0){
  // 		localStorage.setItem("assetsToAssign", JSON.stringify(selectedAssets));
  // 		window.location.href = "../views/assign-user.php";
  // 	}
  // }

  function addActionsButton() {  
    for (const row of rows) {
      row.innerHTML += `
      <td class="actions">
        <button class="action-btn">
          <span class="material-icons">more_horiz</span>
        </button>
        
        <div class="action-menu">
          <a class="menu-item" id="view-action">View</a>
          <a class="menu-item" id="modify-action">Modify</a>
          <a class="menu-item" id="delete-action">Delete</a>
          <a class="menu-item" id="assign-action">Assign</a>
        </div>
      </td>
      `;

      row.querySelectorAll(".action-btn").forEach(btn => {
        btn.addEventListener("click", (e) => {
          e.stopPropagation();
          const menu = btn.parentElement.querySelector(".action-menu");
          menu.style.display = menu.style.display == "flex"? "none" : "flex";
        });
      });
    }
  
    document.addEventListener("click", () => {
      document.querySelectorAll(".action-menu").forEach(menu => {
        menu.style.display = "none";
      });
    });
  }
  

  function getPropNumSelected() {
    let propNums = Array.from([]);
  
    document.querySelectorAll("#selectable-row").forEach(elem => {
      if (elem.firstElementChild.textContent == "check_box") {
        const parentRow = elem.parentElement.parentElement;
        propNums.push(parentRow.children[0].textContent.trim());
        }
      }
    )
    return propNums;
  }
    
  multiSelectButton.addEventListener("click", () => {
    isMultiSelect = !isMultiSelect
    const icon = multiSelectButton.querySelector(".material-icons");
    icon.textContent = isMultiSelect? "check_box" : "check_box_outline_blank";
  
    const tableElement = document.querySelector(".asset-table");
    if (isMultiSelect) {
      const tableHead = tableElement.querySelector("thead tr");
      let i = tableHead.childElementCount;
  
      tableHead.children[i - 1].innerHTML = 
        `<button id="select-all">
          <span class="material-icons"> select_all </span>
          </button>`;
      
      rows.forEach(row => {
        // const cells = row.querySelectorAll("td");
        // const statusCell = cells[5];
        // const status = statusCell.textContent.trim();
        // if (status == "Unused"){
  
        row.querySelector(`td[class="actions"]`).innerHTML = `
          <button id="selectable-row">
            <span class="material-icons"> check_box_outline_blank </span>
          </button>
        `
        // }
      })
  
      document.querySelectorAll("#selectable-row").forEach(elem => {
        elem.addEventListener("click", () => {
          const icon = elem.querySelector(".material-icons");
          icon.textContent = icon.textContent == "check_box" ? "check_box_outline_blank" : "check_box";
        })
      });
  
      const selectAllBtn = document.getElementById("select-all");
      selectAllBtn.addEventListener("click", () => {
        selectedAll = !selectedAll;
        document.querySelectorAll("#selectable-row").forEach(elem => {
          elem.querySelector(".material-icons").textContent = selectedAll ? "check_box" : "check_box_outline_blank";
        })
      });
      
      const tableFuncs = document.querySelector(".table-func");
  
      const assignButton = document.createElement("button");
      assignButton.className = "assign";
      assignButton.innerHTML = `<span class="material-icons">assignment_ind</span>`;
      assignButton.addEventListener('click', () => {
        selectedAssets = getPropNumSelected();
        if (selectedAssets.length != 0){
          localStorage.setItem("assetsToAssign", JSON.stringify(selectedAssets));
          window.location.href = "../views/assign-user.php";
        }
      });
      tableFuncs.prepend(assignButton);  
      
      const deleteButton = document.createElement("button");
      deleteButton.className = "delete";
      deleteButton.innerHTML = `<span class="material-icons">delete</span>`;
      deleteButton.addEventListener('click', () => {
        for (const propNum of getPropNumSelected()) {
          deleteAssets(propNum);
        }
      });
      tableFuncs.prepend(deleteButton);

      
    } else {
      document.querySelectorAll("#select-all").forEach(elem => {
        elem?.remove();
      })
  
      rows.forEach(row => {
        row.querySelector(`td[class="actions"]`).remove();
      })
  
      document.querySelector(".table-func .assign")?.remove();
      document.querySelector(".table-func .delete")?.remove();
      addActionsButton();
    }
  });
});

function addTableFuncs() {
  const leftAsset = document.querySelector(".left-asset");
  const tableContainer = leftAsset.querySelector(".table-container");

  const tableFuncs = document.createElement("div");
  tableFuncs.className = "table-func";
  tableFuncs.innerHTML = `
    <button id="multi-select">
      <span class="material-icons"> check_box_outline_blank </span>
    </button>
    <button id="sort-by">
      <span class="material-icons"> sort </span>
    </button>
  `;

  leftAsset.insertBefore(tableFuncs, tableContainer);
}

function addAssetAdd() {
  const leftAsset = document.querySelector(".left-asset");

  const assetAdd = document.createElement("a");
  assetAdd.href = "asset-form.php";
  assetAdd.id = "addAsset";
  assetAdd.innerHTML = `
    <span class="material-icons" id="add-asset-button">add</span>
    Add a New Asset 
  `;

  leftAsset.append(assetAdd);
}
