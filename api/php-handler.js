const phpServer = require("php-server");
const path = require("path");

module.exports = (req, res) => {
  // Start PHP server only if it's not already running
  const server = phpServer({
    port: 8080,
    root: path.join(__dirname, "../public"),
    open: false,
    quiet: true,
  });

  // Allow the server to continue running
  server.listen(8080, () => {
    res.status(200).send("PHP server started");
  });
};
