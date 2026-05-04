export default function Shop({ products }) {
    return (
        <div style={{ padding: '2rem' }}>
            <h1>React está funcionando</h1>
            <p>Productos recibidos: {products.length}</p>
        </div>
    )
}