import phpServer from 'php-server';
phpServer({
    port: 8000,
    router: './api/routes.php',  
    open: true,
});
console.log(`PHP server running at port 8000`);