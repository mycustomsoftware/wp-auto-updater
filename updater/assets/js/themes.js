(() => {
	document.addEventListener("DOMContentLoaded", (() => {
		const t = wp.themes = wp.themes || {};
		const themeIgnoreUpdates = window.themeIgnoreUpdates = window.themeIgnoreUpdates || {
			ignore_updates:[],
			i18n:{
				disable_auto_update : "Disable auto-update",
				enable_auto_update : "Enable auto-update"
			}
		};
		if(typeof t.data == "undefined"){
			return;
		}
		if(typeof t.data.themes == "undefined"){
			return;
		}
		document.body.addEventListener("click", (e => {
			if(e.target.matches(".wpr-theme-updater")){
				e.preventDefault();
				window.location.href = e.target.getAttribute("href");
			}
		}))
		const observer = new MutationObserver((e => {
			for (let a of e) "childList" === a.type && a.addedNodes.forEach((e => {
				if (e.matches && e.matches(".theme-overlay")) {
					const themes = t.data.themes;
					const e = new URLSearchParams(window.location.search).get("theme");
					const theme = find(e,themes);
					let is_acive = true;
					themeIgnoreUpdates.ignore_updates.find((name) => {
						if(name === theme.id){
							is_acive = false;
						}
					});
					let label = (is_acive ? themeIgnoreUpdates.i18n.disable_auto_update : themeIgnoreUpdates.i18n.enable_auto_update);
					let action = is_acive ? 'activate=true' : 'deactivate=true';
					const button_template = `
					<a class="button" href="options-general.php?page=wp-manage-updates-action&type=theme&file=${theme.id}&${action}">${label}</a>`;
					document.querySelector(".theme-wrap .theme-actions").insertAdjacentHTML("beforeend", button_template)
				}
			}))
		}))
		const find = (what,where) => {
			return Array.isArray(where) ? where.find((itm => itm.id === what)) || null : false;
		};
		const wrap = document.querySelector(".wrap");
		observer.observe(wrap, {childList: !0, subtree: !0})
		// document.querySelector(".theme-wrap .theme-actions").insertAdjacentHTML("beforeend", o)
	}));
})();
