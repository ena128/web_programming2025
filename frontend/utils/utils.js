const Utils = {


    parseJwt: function (token) {
        if (!token) return null;

        const parts = token.split('.');
        if (parts.length < 2) return null;

        const base64Url = parts[1];
        const base64 = base64Url.replace(/-/g, "+").replace(/_/g, "/");

        try {
            const jsonPayload = decodeURIComponent(
                atob(base64)
                    .split("")
                    .map(c => "%" + ("00" + c.charCodeAt(0).toString(16)).slice(-2))
                    .join("")
            );

            const payload = JSON.parse(jsonPayload);

          
            if (payload.user) return payload.user;

            return payload;

        } catch (e) {
            console.error("JWT parsing failed:", e);
            return null;
        }
    },

  
    isLoggedIn: function () {
        const token = localStorage.getItem("user_token");
        if (!token || token === "undefined") return false;

        const payload = this.parseJwt(token);
        return !!payload;
    },


    getUserRole: function () {
        const token = localStorage.getItem("user_token");
        if (!token) return null;

        const payload = this.parseJwt(token);
        if (!payload || !payload.role) return null;

        return payload.role.toLowerCase();
    },


    logout: function () {
        localStorage.removeItem("user_token");
    }
};
