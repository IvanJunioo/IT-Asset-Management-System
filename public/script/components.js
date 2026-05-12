import { relayPage } from "./asset-router.js";
import { fetchLogs } from "./api.js";

export class Pagination {
  constructor({ container, pageCount, onPageChange, curPage = 1}) {
    this.container = container;
    this._curPage = curPage;
    this._pageCount = pageCount;
    this.onPageChange = onPageChange;

    this.prevBtn = document.createElement("button");
    this.prevBtn.textContent = "Prev";
    this.prevBtn.onclick = () => {
      this.curPage--;   // renders automatically
      this.onPageChange();
    };

    this.infoSpan = document.createElement("span");
    
    this.nextBtn = document.createElement("button");
    this.nextBtn.textContent = "Next";
    this.nextBtn.onclick = () => {
      this.curPage++;
      this.onPageChange();
    };

    this.container.replaceChildren(
      this.prevBtn, 
      this.infoSpan, 
      this.nextBtn,
    );

    this.render();
  }

  get curPage() {return this._curPage;}  
  get pageCount() {return this._pageCount;}

  set curPage(page) {
    this._curPage = page;
    this.render();
  }
  set pageCount(count) {
    this._pageCount = count;
    this.render();
  }

  render() {
    this.prevBtn.disabled = this.curPage <= 1;
    this.infoSpan.textContent = `Page ${this.curPage} of ${this.pageCount}`;
    this.nextBtn.disabled = this.pageCount <= this.curPage || this.pageCount === 0;
  }
}

export class LogTable {
  constructor({
    container,
    message = "",   // message substr to look for in logs
    actorID = null, // actor id to look for in logs
    metadata = "",  // substr to look for in logs' metadata 
  }) {
    this.container = container;
    this.message = message;
    this.actorID = actorID;
    this.metadata = metadata;
    this.pageSize = 10;
    this.latestFetchId = 0;   // avoids race condition 
  
    this.table = document.createElement("table");
    this.table.id = "actlog-table";
    this.table.innerHTML = `
      <thead>
        <tr>
          <th> Timestamp </th>
          <th> Employee Name </th>
          <th> Description </th>
        </tr>
      </thead>
      <tbody></tbody>
    `;
    this.tbody = this.table.querySelector("tbody");

    this.paginationDiv = document.createElement("div");
    this.paginationDiv.id = "pagination";
    this.pagination = new Pagination({
      container: this.paginationDiv,
      onPageChange: () => this.load(),
    });

    this.container.replaceChildren(
      this.table,
      this.paginationDiv,
    );

    this.table.addEventListener("click", (e) => {
      const tr = e.target.closest("tr");
      if (!tr) return;
    
      const a = e.target.closest("a");
      if (!a) return;
    
      switch (a.dataset.type) {
        case "actor":
          relayPage("edit-user-form", {"empID": this.tableData.get(Number(tr.dataset.logid)).ActorID});
          break;
        case "asset":
          relayPage("asset-view", {"propNum": tr.dataset.objid});
          break;
        case "user":
          relayPage("edit-user-form", {"empID": Number(tr.dataset.objid)});
          break;
        default:
          console.warn(`Unknown object type: ${a.dataset.type}`);
      }
    });

    this.load();
  }

  async load() {
    const fetchID = ++this.latestFetchId;
    try {
      const data = await fetchLogs({
        message: this.message,
        actorID: this.actorID,
        metadata: this.metadata,
        page: this.pagination.curPage,
        pageSize: this.pageSize,
      });

      if (fetchID !== this.latestFetchId) return;

      this.tableData = new Map(data["logs"].map(log => [log.LogID, log]));
      this.rowCount = Number(data["count"]);
      
      this.pagination.pageCount = Math.ceil(this.rowCount / this.pageSize);
      this.render();
    }
    catch (err) {
      console.error("Error fetching logs:", err);
    }
  }

  render() {
    if (this.tableData.size <= 0) {
      this.tbody.innerHTML = `
        <tr>
          <td colSpan="${this.table.querySelector("thead tr").children.length}" style="text-align: center;"> No logs to display. </td>
        </tr>
      `;
      return;
    }

    this.tbody.innerHTML = "";

    for (const [_, log] of this.tableData) {
      const tr = document.createElement("tr");
      
      const metadata = JSON.parse(log.Metadata);

      const objID = {
        "asset": metadata["propNum"],
        "user": metadata["empID"],
      }[metadata["object"]];

      const objName = {
        "asset": metadata["propNum"],
        "user": log.objName,
      }[metadata["object"]];

      // Store id
      tr.dataset.logid = log.LogID;
      tr.dataset.objid = objID;

      const action = {
        "modify": "modified",
        "deactivate": "deactivated",
        "activate" : "activated"
      }[metadata["action"]] || `${metadata["action"]}ed`;

      const actorHTML = text => log.linkActor? `<a data-type="actor">${text}</a>`: text;
      const objHTML = text => log.linkObject? `<a data-type="${metadata["object"]}">${text}</a>` : text;
      
      const extra = metadata.hasAssets? " (has assigned assets)" : "";

      for (const col of [
        log.Timestamp,
        actorHTML(`${log.FName} ${log.LName}`),
        `${actorHTML(`${log.FName[0].toUpperCase()}. ${log.LName}`)} ${action} ${metadata["object"]} ${objHTML(objName)} ${extra}`,
      ]) {
        const td = document.createElement("td");
        td.innerHTML = col;
        tr.appendChild(td);
      }

      this.tbody.appendChild(tr);
    }
  }
}
