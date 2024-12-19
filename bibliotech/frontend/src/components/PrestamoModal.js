import React, { useState } from 'react';

function PrestamoModal({ libro, usuarios, onConfirm, onClose }) {
  const [selectedUserId, setSelectedUserId] = useState('');
  const fechaDevolucion = new Date();
  fechaDevolucion.setDate(fechaDevolucion.getDate() + 15);

  const handleConfirm = () => {
    if (selectedUserId) {
      console.log('Realizando préstamo:', {
        libro_id: libro.id,
        usuario_id: selectedUserId,
        fecha_prestamo: new Date().toISOString().split('T')[0],
        fecha_devolucion: fechaDevolucion.toISOString().split('T')[0]
      });
      onConfirm(selectedUserId);
    }
  };
  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
      <div className="bg-white rounded-lg p-8 max-w-md w-full">
        <h2 className="text-2xl font-bold mb-6">Confirmar Préstamo</h2>
        <div className="space-y-4">
          <div>
            <h3 className="font-semibold">Libro:</h3>
            <p>{libro.titulo}</p>
            <p className="text-sm text-gray-600">Autor: {libro.autor}</p>
          </div>
          <div>
            <h3 className="font-semibold">Seleccionar Usuario:</h3>
            <select
              value={selectedUserId}
              onChange={(e) => setSelectedUserId(e.target.value)}
              className="w-full border rounded-lg px-4 py-2 mt-2"
            >
              <option value="">Seleccione un usuario</option>
              {usuarios.map(usuario => (
                <option key={usuario.id} value={usuario.id}>
                  {usuario.nombre} ({usuario.tipo})
                </option>
              ))}
            </select>
          </div>
          <div>
            <h3 className="font-semibold">Fecha de préstamo:</h3>
            <p>{new Date().toLocaleDateString()}</p>
          </div>
          <div>
            <h3 className="font-semibold">Fecha de devolución:</h3>
            <p>{fechaDevolucion.toLocaleDateString()}</p>
          </div>
        </div>
        <div className="flex justify-end gap-4 mt-6">
          <button
            onClick={onClose}
            className="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300"
          >
            Cancelar
          </button>
          <button
            onClick={handleConfirm}
            disabled={!selectedUserId}
            className={`px-4 py-2 rounded-lg ${
              selectedUserId 
                ? 'bg-blue-600 text-white hover:bg-blue-700' 
                : 'bg-gray-300 text-gray-500 cursor-not-allowed'
            }`}
          >
            Confirmar Préstamo
          </button>
        </div>
      </div>
    </div>
  );
}

export default PrestamoModal;