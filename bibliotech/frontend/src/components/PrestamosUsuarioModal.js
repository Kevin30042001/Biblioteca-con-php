import React from 'react';

function PrestamosUsuarioModal({ usuario, prestamos = [], onClose }) {
  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
      <div className="bg-white rounded-lg p-8 max-w-4xl w-full">
        <h2 className="text-2xl font-bold mb-6">Préstamos de {usuario?.nombre}</h2>
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Libro</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Préstamo</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Devolución</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
  {prestamos && prestamos.length > 0 ? (
    prestamos.map((prestamo, index) => (
      <tr key={index}>
        <td className="px-6 py-4">{prestamo.libro_titulo}</td>
        <td className="px-6 py-4">{prestamo.fecha_prestamo}</td>
        <td className="px-6 py-4">{prestamo.fecha_devolucion}</td>
        <td className="px-6 py-4">
          <span className={`px-2 py-1 rounded-full text-xs ${
            prestamo.estado === 'Devuelto' 
              ? 'bg-green-100 text-green-800' 
              : 'bg-yellow-100 text-yellow-800'
          }`}>
            {prestamo.estado}
          </span>
        </td>
      </tr>
    ))
  ) : (
    <tr>
      <td colSpan="4" className="px-6 py-4 text-center text-gray-500">
        No hay préstamos registrados
      </td>
    </tr>
  )}
</tbody>
          </table>
        </div>
        <div className="flex justify-end mt-6">
          <button
            onClick={onClose}
            className="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  );
}

export default PrestamosUsuarioModal;