const phpServer = require("php-server");
const path = require("path");

module.exports = (req, res) => {
  // Start PHP server and serve the PHP files
  phpServer({
    port: 8080,
    root: path.join(__dirname, "../public"),
    open: false,
    quiet: true,
  });

  // Respond to requests from Vercel
  res.status(200).send("PHP server started");
};
