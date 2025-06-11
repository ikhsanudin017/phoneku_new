import axios from 'axios'

// ...existing code...

// Products API endpoints
export const productsAPI = {
    getAll: async (params = {}) => {
        return performApiRequest({
            url: '/products',
            method: 'get',
            params
        })
    },
    getProduct: async (id) => {
        return performApiRequest({
            url: `/products/${id}`,
            method: 'get'
        })
    },
    createProduct: async (productData) => {
        return performApiRequest({
            url: '/products',
            method: 'post',
            data: productData,
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
    },
    updateProduct: async (id, productData) => {
        return performApiRequest({
            url: `/products/${id}`,
            method: 'put',
            data: productData,
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
    },
    deleteProduct: async (id) => {
        return performApiRequest({
            url: `/products/${id}`,
            method: 'delete'
        })
    }
}

// Update the default export to include productsAPI
export default {
    authAPI,
    adminAPI,
    cartAPI,
    productsAPI,
    performApiRequest
}


