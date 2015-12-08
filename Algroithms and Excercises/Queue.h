#ifndef G_QUEUE_H
#define G_QUEUE_H
#include <iostream>

template <typename T>
class Queue { //FIFO

private:
    class Node {
        friend class Queue<T>;

    private:
        T data;
        Node* next;

    public:
        Node(T d, Node* n = NULL) : data(d), next(n) {}
		void log(){	std::cout<<data<<std::endl; }

	};

    Node *head,*tail;  // End of list
    int size;    // Number of nodes in list

public:

    Queue(const Queue<T>& src);  // Copy constructor
    ~Queue();  // Destructor

    Queue() : head(NULL), tail(NULL), size(0) {}

    // Returns a reference to first element
    T& front() {
        return head->data;
    }

    // Returns a reference to last element
    T& back() {
        return tail->data;
    }
    Queue<T>& enqueue(T);   // Insert element at end
    Queue<T>& dequeue();  // Remove element from beginning

	Node* findByIndex(int i);
    void dump();  // Output contents of list
};

// Copy constructor
template <typename T>
Queue<T>::Queue(const Queue<T>& src) :
        head(NULL), tail(NULL), size(0) {

    Node* current = src.head;
    while (current != NULL) 
	{
        this->push_back(current->data);
        current = current->next;
    }

}

// Destructor
template <typename T>
Queue<T>::~Queue() 
{
    while (size!=0) 
		this->dequeue();   
}


// Insert an element at the end
template <typename T>
Queue<T>& Queue<T>::enqueue(T d) {

    Node* new_tail = new Node(d, NULL);

    if (size==0) 
        head = new_tail;
     else 
        tail->next = new_tail;

    tail = new_tail;
    ++size;
	return *this;
}

// Remove an element from the beginning
template <typename T>
Queue<T>& Queue<T>::dequeue() 
{
    Node* old_head = head;

    if (size==1) 
	{
        head = NULL;
        tail = NULL;
    } 
	else 
        head = head->next;
    
    delete old_head;
    --size;
	return *this;
}
 


// Display the contents of the list
template <typename T>
void Queue<T>::dump() {

    std::cout << "(";

    Node* current = head;

    if (current != NULL) 
        while (current->next != NULL) 
		{
            std::cout << current->data << ", ";
            current = current->next;
        }
        std::cout << current->data;

    std::cout << ")" << std::endl;
}


template <typename T>
typename Queue<T>::Node* Queue<T>::findByIndex(int i) {
	int counter = 0;
	Node* current = head;
	if (current != NULL) 
		for (;current->next != NULL;current=current->next) 
		{
			if(counter == i) return current;
			++counter;
		}
 return nullptr;
}


#endif