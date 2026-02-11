<?php

namespace App\Http\Controllers;

use App\Models\UserDocument;
use App\Models\User;
use App\Notifications\DocumentStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminDocumentController extends Controller
{
    /**
     * Display a listing of all user documents pending approval.
     */
    public function index()
    {
        $documents = UserDocument::with(['user', 'requiredDocument'])
            ->whereIn('status', ['pendiente', 'rechazado'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.documents.index', compact('documents'));
    }

    /**
     * Display all documents for a specific user.
     */
    public function userDocuments($userId)
    {
        $user = User::findOrFail($userId);
        $documents = UserDocument::with('requiredDocument')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.documents.user', compact('user', 'documents'));
    }

    /**
     * Update the status of a document (approve or reject).
     */
    public function updateStatus(Request $request, UserDocument $document)
    {
        $request->validate([
            'status' => 'required|in:aprobado,rechazado',
            'comments' => 'nullable|string|max:500'
        ]);

        $oldStatus = $document->status;
        
        $document->update([
            'status' => $request->status,
            'comments' => $request->comments
        ]);

        Log::info("Document status updated", [
            'document_id' => $document->id,
            'user_id' => $document->user_id,
            'old_status' => $oldStatus,
            'new_status' => $request->status,
            'comments' => $request->comments
        ]);

        // Send notification to the user
        try {
            $document->user->notify(new DocumentStatusChanged($document));
            Log::info("DocumentStatusChanged notification sent", [
                'user_id' => $document->user_id,
                'document_id' => $document->id
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send document notification", [
                'error' => $e->getMessage(),
                'document_id' => $document->id
            ]);
        }

        // Check if all user documents are approved to update user verification status
        $this->checkUserVerification($document->user);

        return back()->with('success', 'Estado del documento actualizado correctamente.');
    }

    /**
     * Approve a document.
     */
    public function approve(Request $request, UserDocument $document)
    {
        $request->validate([
            'comments' => 'nullable|string|max:500'
        ]);

        $document->update([
            'status' => 'aprobado',
            'comments' => $request->comments
        ]);

        // Send notification
        $document->user->notify(new DocumentStatusChanged($document));

        // Check user verification
        $this->checkUserVerification($document->user);

        return back()->with('success', 'Documento aprobado correctamente.');
    }

    /**
     * Reject a document.
     */
    public function reject(Request $request, UserDocument $document)
    {
        $request->validate([
            'comments' => 'required|string|max:500'
        ]);

        $document->update([
            'status' => 'rechazado',
            'comments' => $request->comments
        ]);

        // Send notification
        $document->user->notify(new DocumentStatusChanged($document));

        // Update user verification status
        $document->user->update(['verified' => false]);

        return back()->with('success', 'Documento rechazado correctamente.');
    }

    /**
     * Check if all user documents are approved and update verification status.
     */
    private function checkUserVerification(User $user)
    {
        $totalDocuments = $user->documents()->count();
        $approvedDocuments = $user->documents()->where('status', 'aprobado')->count();

        if ($totalDocuments > 0 && $totalDocuments === $approvedDocuments) {
            $user->update(['verified' => true]);
            Log::info("User verified", ['user_id' => $user->id]);
        } else {
            $user->update(['verified' => false]);
            Log::info("User unverified", ['user_id' => $user->id]);
        }
    }

    /**
     * Show pending documents count for admin dashboard.
     */
    public function pendingCount()
    {
        return UserDocument::where('status', 'pendiente')->count();
    }
}
