import Axios from 'axios';
import React, { useEffect, useState } from 'react';

import axios from 'axios';



const CustomersPage = (props) => {


    const [customers, setCustomers] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);

    useEffect(() => {
        axios.get("https://127.0.0.1:8000/api/customers")
            .then(response => response.data["hydra:member"])
            .then(data => setCustomers(data))
            .catch(error => console.log(error.response));
    }, []);

    const handleDelete = (id) => {
        console.log(id);

        const originalCustomers = [...customers];
        // 1 l'approche optimiste
            // Permet l'instantanéité du click mais rogne sur la sureté si un catch ne sécurise pas la requette 
        setCustomers(customers.filter(customer => customer.id !== id));
       

        // 2 l'approche pessismiste
            // En cas d'erreur, setCustomers renvoi la copie du tableau des customers.
        axios.delete("https://127.0.0.1:8000/api/cusmers/" + id)
            .then(response =>  console.log("OK"))
            .catch(error => {
                setCustomers(originalCustomers);
                console.log(error.response);
            })
    };


    const handlePageChange =  (page) => {
        setCurrentPage(page);
    }


    const itemsPerPage = 10;
    const pagesCount = Math.ceil(customers.length / itemsPerPage);

    const pages = [];

    for (let i = 1; i < pagesCount; i++) {
        pages.push(i);
        
    }

    // d'ou on part (start)
    // pendant combien (itemsPerPage)
    const start = currentPage  * itemsPerPage - itemsPerPage;
    //            id page:  3  * item par page:10 - 10 item par page   = 20 affichage 
    const paginatedCustomers = customers.slice(start , start + itemsPerPage)

    console.log(pagesCount);


    return ( 
        <>
            <h1>Les des clients</h1> 
            <table className="table table-hover">
                <thead>
                    <tr>
                        <th>Id.</th>
                        <th>Client</th>
                        <th>Email</th>
                        <th>Entreprise</th>
                        <th  className="text-center">Factures</th>
                        <th  className="text-center">Montant total</th>
                        <th>Options</th>
                    </tr>   
                </thead>

                <tbody>
                    {paginatedCustomers.map(customer => 
                     <tr key={customer.id}>
                        <td>{customer.id}</td>
                        <td><a href="#">{customer.firstName} {customer.lastName}</a></td>
                        <td>{customer.email}</td>
                        <td>{customer.company}</td>
                        <td className="text-center">
                            <span className="badge badge-light">
                             { customer.invoices.length }
                            </span>
                        </td>
                    <td className="text-center">{ customer.totalAmount.toLocaleString() } €</td>
                        <td>
                            <button 
                            onClick={() => handleDelete(customer.id) }
                            disabled={ customer.invoices.length > 0 } 
                            className="btn btn-danger"
                            >Supprimer</button>
                        </td>
                    </tr>
                     )}
                   
                </tbody>
            </table>


            <div>
            <ul className="pagination pagination-sm">
                <li className={"page-item" + ( currentPage === 1 && " disabled")}>
                <button 
                    className="page-link" 
                    href="#"
                    onClick={() => handlePageChange(currentPage - 1)}
                    >&laquo;
                </button>
                </li>
                {pages.map(page => 

                    <li key={page}
                        className={"page-item" + (currentPage === page && " active")}
                    >

                        <button 
                        className="page-link" 
                        href="#"
                        onClick={() => handlePageChange(page)}
                        >{page }</button>
                    </li>

                )}
               
                <li className={"page-item" + ( currentPage === pagesCount - 1 && " disabled")}>
                <button 
                    className="page-link" 
                    href="#"
                    onClick={() => handlePageChange(currentPage + 1)}
                    >&raquo;
                </button>
                </li>
            </ul>
            </div>
        </>
    );
}
 
export default CustomersPage;